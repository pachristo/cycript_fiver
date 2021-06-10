<?php

namespace App\Http\Controllers\admin;

use App\Http\Requests\Admin\GiveCoinRequest;
use App\Model\AdminGiveCoinHistory;
use App\Model\BuyCoinHistory;
use App\Model\Coin;
use App\Model\Wallet;
use App\Repository\AffiliateRepository;
use App\Services\CoinPaymentsAPI;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class CoinController extends Controller
{
    // admin pending order
    public function adminPendingCoinOrder(Request $request)
    {
        $data['title'] = __('Buy Coin Order List');
        if ($request->ajax()) {
            $deposit = BuyCoinHistory::select('*')->where(['status' => STATUS_PENDING]);

            return datatables()->of($deposit)
                ->addColumn('payment_type', function ($dpst) {
                    $html  = '';
                    if ($dpst->type == BANK_DEPOSIT) {
                        $html .= receipt_view_html(imageSrc($dpst->bank_sleep,IMG_SLEEP_VIEW_PATH));
                    } else {
                        $html .= byCoinType($dpst->type);
                    }

                    return $html;
                })
                ->addColumn('email', function ($dpst) {
                    return isset($dpst->user()->first()->email) ? $dpst->user()->first()->email : '';
                })
                ->addColumn('btc', function ($dpst) {
                    return $dpst->btc.' '.$dpst->coin_type;
                })
                ->addColumn('action', function ($wdrl) {
                    $action = '<ul>';
                    $action .= accept_html('adminAcceptPendingBuyCoin',encrypt($wdrl->id));
                    $action .= reject_html('adminRejectPendingBuyCoin',encrypt($wdrl->id));
                    $action .= '<ul>';
                    return $action;
                })
                ->rawColumns(['payment_type','action'])
                ->make(true);
        }

        return view('admin.coin-order.pending_list', $data);
    }

    // admin approved order
    public function adminApprovedOrder(Request $request)
    {
        if ($request->ajax()) {
            $deposit = BuyCoinHistory::select('*')->where(['status' => STATUS_ACTIVE]);

            return datatables()->of($deposit)
                ->addColumn('payment_type', function ($dpst) {
                    $html  = '';
                    if ($dpst->type == BANK_DEPOSIT) {
                        $html .= receipt_view_html(imageSrc($dpst->bank_sleep,IMG_SLEEP_VIEW_PATH));
                    } else {
                        $html .= byCoinType($dpst->type);
                    }

                    return $html;
                })
                ->addColumn('email', function ($dpst) {
                    return $dpst->user()->first()->email;
                })
                ->addColumn('btc', function ($dpst) {
                    return $dpst->btc.' '.$dpst->coin_type;
                })
                ->rawColumns(['payment_type'])
                ->make(true);
        }

        return view('admin.coin-order.pending_list');
    }

    // admin rejected order
    public function adminRejectedOrder(Request $request)
    {
        if ($request->ajax()) {
            $deposit = BuyCoinHistory::select('*')->where(['status' => STATUS_REJECTED]);

            return datatables()->of($deposit)
                ->addColumn('payment_type', function ($dpst) {
                    $html  = '';
                    if ($dpst->type == BANK_DEPOSIT) {
                        $html .= receipt_view_html(imageSrc($dpst->bank_sleep,IMG_SLEEP_VIEW_PATH));
                    } else {
                        $html .= byCoinType($dpst->type);
                    }

                    return $html;
                })
                ->addColumn('email', function ($dpst) {
                    return $dpst->user()->first()->email;
                })
                ->addColumn('btc', function ($dpst) {
                    return $dpst->btc.' '.$dpst->coin_type;
                })
                ->editColumn('created_at', function ($dpst) {
                    return $dpst->created_at;
                })

                ->rawColumns(['payment_type'])
                ->make(true);
        }

        return view('admin.coin-order.pending_list');
    }

    // pending coin accept process
    public function adminAcceptPendingBuyCoin($id)
    {
        if (isset($id)) {
            try {
                $wdrl_id = decrypt($id);
            } catch (\Exception $e) {
                return redirect()->back();
            }
            DB::beginTransaction();
            try {
                $affiliate_servcice = new AffiliateRepository();
                $transaction = BuyCoinHistory::where(['id' => $wdrl_id, 'status' => STATUS_PENDING])->firstOrFail();

                $primary = get_primary_wallet($transaction->user_id, 'Default');

                $primary->increment('balance', $transaction->coin);
                $transaction->status = STATUS_SUCCESS;
                $transaction->save();

                if (!empty($transaction->phase_id)) {
                    $bonus = $affiliate_servcice->storeAffiliationHistoryForBuyCoin($transaction);
                }
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->back()->with('dismiss', 'Something went wrong');
            }

            DB::commit();
            return redirect()->back()->with('success', 'Request accepted successfully');
        }
    }

    // pending coin reject process
    public function adminRejectPendingBuyCoin($id)
    {
        if (isset($id)) {
            try {
                $wdrl_id = decrypt($id);
            } catch (\Exception $e) {
                return redirect()->back();
            }
            $transaction = BuyCoinHistory::where(['id' => $wdrl_id, 'status' => STATUS_PENDING])->firstOrFail();
            $transaction->status = STATUS_REJECTED;
            $transaction->update();

            return redirect()->back()->with('success', 'Request cancelled successfully');
        }
    }

    // give coin page
    public function giveCoinToUser()
    {
        $data['title'] = __('Give coin to user');
        $data['users'] = User::where(['role'=>USER_ROLE_USER, 'status'=>STATUS_ACTIVE])->get();

        return view('admin.coin-order.give_coin', $data);
    }

    // give coin process
    public function giveCoinToUserProcess(GiveCoinRequest $request)
    {
        try {
            if ($request->amount <= 0) {
                return redirect()->back()->withInput()->with('dismiss', __('Minimum coin amount is 1'));
            }
            if ($request->amount > 10000000) {
                return redirect()->back()->withInput()->with('dismiss', __('Maximum coin amount is 10000000'));
            }
            if (isset($request->user_id[0])) {
                DB::beginTransaction();
                foreach ($request->user_id as $key => $value) {
                    $user = User::where('id', $value)->first();
                    $wallet = Wallet::where(['user_id'=> $value, 'coin_type' => 'Default', 'is_primary' => STATUS_ACTIVE])->first();
                    if (isset($user) && isset($wallet)) {
                        $wallet->increment('balance', $request->amount);
                        $this->saveGiveCoinHistory($user->id, $wallet->id,$request->amount);
                    }
                }
                DB::commit();
                return redirect()->back()->with('success', __('Coin send successfully'));
            } else {
                return redirect()->back()->withInput()->with('dismiss', __('Please select at least one user'));
            }


        } catch (\Exception $e) {
            DB::rollBack();
//            return redirect()->back()->with('dismiss', __('Something went wrong'));
            return redirect()->back()->with('dismiss', $e->getMessage());
        }
    }

    // save give coin history
    public function saveGiveCoinHistory($user_id, $wallet_id, $amount)
    {
        try {
            AdminGiveCoinHistory::create(['user_id' => $user_id, 'wallet_id' => $wallet_id, 'amount'=> $amount]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    // admin give coin list
    public function giveCoinHistory(Request $request)
    {
        $data['title'] = __('Give coin history');
        if ($request->ajax()) {
            $items = AdminGiveCoinHistory::join('users', 'users.id', '=', 'admin_give_coin_histories.user_id')
                ->select('admin_give_coin_histories.*', 'users.email as email');

            return datatables()->of($items)
                ->addColumn('wallet_id', function ($item) {
                    return !empty($item->wallet->name) ? $item->wallet->name : 'N/A';
                })
                ->make(true);
        }

        return view('admin.coin-order.give_coin_history', $data);
    }

    // all coin list
    public function adminCoinList(Request $request)
    {
        try {
            $coinpayment = new CoinPaymentsAPI();
            $api_rate = $coinpayment->GetRates('');

            if ( ($api_rate['error'] == "ok") ) {
                $active_coins = [];
                foreach($api_rate['result'] as $key => $result) {
                    if ($result['accepted'] == 1) {
                        $active_coins[$key] = [
                            'coin_type' => $key,
                            'name' => $result['name'],
                            'accepted' => $result['accepted']
                        ];
                    }
                }
                if (isset($active_coins)) {
                    foreach($active_coins as $key => $active) {
                        Coin::updateOrCreate(['type' => $active['coin_type']], ['name' => $active['name'], 'type' => $active['coin_type'], 'status' => STATUS_ACTIVE]);
                    }
                } else {
                    Coin::updateOrCreate(['type' => 'BTC'], ['name' => 'Bitcoin', 'type' => 'BTC', 'status' => STATUS_ACTIVE]);
                }
                $dbCoins = Coin::where('status', '<>', STATUS_DELETED)->orderBy('id','asc')->get();
                $db_coins =[];
                foreach ($dbCoins as $dbc) {
                    $db_coins[$dbc->type] = [
                        'coin_type' => $dbc->type,
                        'name' => $dbc->name,
                        'accepted' => $dbc->status
                    ];
                }
                if (isset($active_coins) && isset($db_coins)) {
                    $inactive_coins = array_diff_key($db_coins, $active_coins);
                }
                if (isset($inactive_coins)) {
                    foreach ($inactive_coins as $key => $value) {
                        Coin::where('type', $key)->update(['status' => STATUS_DELETED]);
                    }
                }
                $data['title'] = __('Coin List');
                $data['coins'] = Coin::where('status', '<>', STATUS_DELETED)->orderBy('id','asc')->get();

                return view('admin.coin-order.coin', $data);
            } else {
                return redirect()->back()->with('dismiss',$api_rate['error']);
            }


        } catch (\Exception $e) {
            return redirect()->back()->with('dismiss', $e->getMessage());
        }
    }

    // change coin status
    public function adminCoinStatus(Request $request)
    {
        $coin = Coin::find($request->active_id);
        if ($coin) {
            if ($coin->status == STATUS_ACTIVE) {
               $coin->update(['status' => STATUS_DEACTIVE]);
            } else {
                $coin->update(['status' => STATUS_ACTIVE]);
            }
            return response()->json(['message'=>__('Status changed successfully')]);
        } else {
            return response()->json(['message'=>__('Coin not found')]);
        }
    }
}
