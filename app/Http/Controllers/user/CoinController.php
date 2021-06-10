<?php

namespace App\Http\Controllers\user;

use App\Http\Requests\btcDepositeRequest;
use App\Model\Bank;
use App\Model\BuyCoinHistory;
use App\Model\Coin;
use App\Model\CoinRequest;
use App\Model\Wallet;
use App\Repository\AffiliateRepository;
use App\Repository\CoinRepository;
use App\Services\CoinPaymentsAPI;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Model\IcoPhase;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Stripe\Charge;
use Stripe\Stripe;

class CoinController extends Controller
{
    // buy coin
    public function buyCoinPage()
    {
        try {
            $data['title'] = __('Buy Coin');
            $data['settings'] = allsetting();
            $data['banks'] = Bank::where(['status' => STATUS_ACTIVE])->get();
            $data['coins'] = Coin::where('status', STATUS_ACTIVE)->get();
            $url = file_get_contents('https://min-api.cryptocompare.com/data/price?fsym=USD&tsyms=BTC');
            $data['coin_price'] = settings('coin_price');
            $data['btc_dlr'] = (settings('coin_price') * json_decode($url,true)['BTC']);
            $data['btc_dlr'] = custom_number_format($data['btc_dlr']);

            $activePhases = checkAvailableBuyPhase();

            $data['no_phase'] = false;
            if ($activePhases['status'] == false) {
                $data['no_phase'] = true;
            } else {
                if ($activePhases['futurePhase'] == false) {
                    $phase_info = $activePhases['pahse_info'];
                    if (isset($phase_info)) {
                        $data['coin_price'] =  number_format($phase_info->rate,4);
                        $data['btc_dlr'] = ($data['coin_price'] * json_decode($url,true)['BTC']);
                        $data['btc_dlr'] = custom_number_format($data['btc_dlr']);
                    }
                }
            }
            $data['activePhase'] = $activePhases;



            return view('user.buy_coin.index',$data);
        } catch (\Exception $e) {
            return redirect()->back();
        }

    }

    public function buyCoinRate(Request $request)
    {
        if ($request->ajax()) {
            $data['amount'] = isset($request->amount) ? $request->amount : 0;

            $data['coin_type'] = isset($request->payment_type) ? check_coin_type($request->payment_type) : allsetting('base_coin_type');

            $coin_price = settings('coin_price');
            $activePhases = checkAvailableBuyPhase();
            $data['phase_fees'] = 0;
            $data['bonus'] = 0;
            $data['no_phase'] = false;
            if ($activePhases['status'] == false) {
                $data['no_phase'] = true;
            } else {
                if ($activePhases['futurePhase'] == false) {

                    $phase_info = $activePhases['pahse_info'];
                    if (isset($phase_info)) {
                        $coin_price =  customNumberFormat($phase_info->rate);
                        $data['phase_fees'] = calculate_phase_percentage($data['amount'], $phase_info->fees);
                        $affiliation_percentage = 0;
                        $data['bonus'] = calculate_phase_percentage($data['amount'], $phase_info->bonus);


                       // $coin_amount = ($data['amount'] + $data['bonus']) - ($data['phase_fees'] + $affiliation_percentage);
                        $coin_amount = ($data['amount'] - $data['bonus']);
                        $data['amount'] = $coin_amount;
                        $data['phase_fees'] = customNumberFormat($data['phase_fees']);
                    }
                }
            }

            $data['coin_price'] = bcmul($coin_price,$data['amount'],8);
            $data['coin_price'] = customNumberFormat($data['coin_price']);
            if ($request->pay_type == BTC) {
                $coinpayment = new CoinPaymentsAPI();
                $api_rate = $coinpayment->GetRates('');


                $data['btc_dlr'] = converts_currency($data['coin_price'], $data['coin_type'],$api_rate);

            } else {
                $data['coin_type'] = allsetting('base_coin_type');
                $url = file_get_contents('https://min-api.cryptocompare.com/data/price?fsym=USD&tsyms=BTC');
                $data['btc_dlr'] = $data['coin_price'] * (json_decode($url,true)['BTC']);
            }

            $data['btc_dlr'] = custom_number_format($data['btc_dlr']);

            return response()->json($data);
        }
    }


    // buy coin process
    public function buyCoin(btcDepositeRequest $request)
    {
        $url = file_get_contents('https://min-api.cryptocompare.com/data/price?fsym=USD&tsyms=BTC');

        if (isset(json_decode($url, true)['BTC'])) {
            $phase_fees = 0;
            $affiliation_percentage = 0;
            $bonus = 0;
            $coin_amount = $request->coin;
            $phase_id = '';
            $referral_level = '';

            if (isset($request->phase_id)) {
                $phase = IcoPhase::where('id',$request->phase_id)->first();
                if (isset($phase)) {
                    $total_sell = BuyCoinHistory::where('phase_id',$phase->id)->sum('coin');
                    if (($total_sell + $coin_amount) > $phase->amount) {
                        return redirect()->back()->with('dismiss', __('Insufficient phase amount'));
                    }
                    $phase_id = $phase->id;
                    $referral_level = $phase->affiliation_level;
                    $phase_fees = calculate_phase_percentage($request->coin, $phase->fees);
                    // $affiliation_percentage = calculate_phase_percentage($request->coin, $phase->affiliation_percentage);
                    $affiliation_percentage = 0;
                    $bonus = calculate_phase_percentage($request->coin, $phase->bonus);
                  //  $coin_amount = ($request->coin + $bonus) - ($phase_fees + $affiliation_percentage);
                    $coin_amount = ($request->coin - $bonus) ;

                    $coin_price_doller = bcmul($coin_amount, $phase->rate,8);
                    $coin_price_btc = bcmul(custom_number_format(json_decode($url, true)['BTC']), $coin_price_doller,8);
//                    $coin_price_btc = custom_number_format($coin_price_btc);

                } else {
                    $coin_price_doller = bcmul($request->coin, settings('coin_price'),8);
                    $coin_price_btc = bcmul(custom_number_format(json_decode($url, true)['BTC']), $coin_price_doller,8);
//                    $coin_price_btc = custom_number_format($coin_price_btc);
                }

            } else {
                $coin_price_doller = bcmul($request->coin, settings('coin_price'),8);
                $coin_price_btc = bcmul(custom_number_format(json_decode($url, true)['BTC']), $coin_price_doller,8);
//                $coin_price_btc = custom_number_format($coin_price_btc);
            }

            if ($request->payment_type == BTC) {

                DB::beginTransaction();
                try {
                    $data['data'] = (object)[]; // placed order
                    $data['success'] = false;
                    $data['message'] = __('Invalid operation');
                    $coin_payment = new CoinPaymentsAPI();

                    $amount = isset($request->coin) ? $request->coin : 0;
                    $coin_type = isset($request->payment_coin_type) ? check_coin_type($request->payment_coin_type) : allsetting('base_coin_type');
                    $address = $coin_payment->GetCallbackAddress($coin_type);

                    if ( isset($address['error']) && ($address['error'] == 'ok') ) {

                        $coinpayment = new CoinPaymentsAPI();
                        $api_rate = $coinpayment->GetRates('');
                        $coin_price_btc = converts_currency($coin_price_doller, $coin_type,$api_rate);

                        if ( $address ) {
                            if (isset($coin_price_btc) && $coin_price_btc > 0) {
                                $btc_transaction = new BuyCoinHistory();
                                $btc_transaction->address = $address['result']['address'];
                                $btc_transaction->type = BTC;
                                $btc_transaction->user_id = Auth::id();
                                $btc_transaction->phase_id = $phase_id;
                                $btc_transaction->referral_level = $referral_level;
                                $btc_transaction->fees  = $phase_fees ;
                                $btc_transaction->bonus = $bonus;
                                $btc_transaction->referral_bonus = $affiliation_percentage;
                                $btc_transaction->requested_amount = $coin_amount;
                                $btc_transaction->coin = $request->coin;
                                $btc_transaction->doller = $coin_price_doller;
                                $btc_transaction->btc = $coin_price_btc;
                                $btc_transaction->coin_type = $coin_type;
                                $btc_transaction->save();

                                $data['data'] = $btc_transaction; // placed order
                                $data['success'] = true;
                                $data['message'] = __('Order placed successfully');
                            } else {
                                $data['data'] = (object)[]; // placed order
                                $data['success'] = false;
                                $data['message'] = __('Coin payment not working properly');
                            }
                        }
                    } else {
                        $data['data'] = (object)[]; // placed order
                        $data['success'] = false;
                        $data['message'] = __('Coin payment address not generated');
                    }



                    DB::commit();
                    if ($data['success'] == false) {
                        return redirect()->back()->with('dismiss', $data['message']);
                    } else {
                        return redirect()->route('buyCoinByAddress', $btc_transaction->address)->with('success', __("Request submitted successful,please send Coin with this address"));
                    }
                } catch (\Exception $e) {
                    DB::rollBack();
//                    return redirect()->back()->with('dismiss', $e->getMessage());
                    return redirect()->back()->with('dismiss', __("Something went wrong"));
                }
            } elseif ($request->payment_type == BANK_DEPOSIT) {
                $btc_transaction = new BuyCoinHistory();
                $btc_transaction->type = BANK_DEPOSIT;
                $btc_transaction->address = 'N/A';
                $btc_transaction->user_id = Auth::id();
                $btc_transaction->doller = $coin_price_doller;
                $btc_transaction->btc = $coin_price_btc;
                $btc_transaction->phase_id = $phase_id;
                $btc_transaction->referral_level = $referral_level;
                $btc_transaction->fees  = $phase_fees ;
                $btc_transaction->bonus = $bonus;
                $btc_transaction->referral_bonus = $affiliation_percentage;
                $btc_transaction->requested_amount = $coin_amount;
                $btc_transaction->coin = $request->coin;
                $btc_transaction->coin_type = "Default";
                $btc_transaction->bank_id = $request->bank_id;
                $btc_transaction->bank_sleep = uploadFile($request->file('sleep'), IMG_SLEEP_PATH);
                $btc_transaction->save();

                return redirect()->back()->with('success', __("Request submitted successful,Please wait for admin approval"));
            } elseif ($request->payment_type = STRIPE) {
                try {
                    $stripe_secret = '';
                    if (!empty(settings()['STRIPE_SECRET'])) {
                        $stripe_secret = settings()['STRIPE_SECRET'];
                    }

                    Stripe::setApiKey($stripe_secret);
                    $charge = Charge::create ([
                        "amount" => $coin_price_doller * 100,
                        "currency" => "usd",
                        "source" => $request->stripeToken,
                        "description" => "Payment from ".Auth::user()->email. ' for '.$coin_price_doller. ' usd'
                    ]);
                } catch (\Exception $e) {
                    return redirect()->back()->with('dismiss', $e->getMessage());
                }
                if (isset($charge) && $charge['status'] == 'succeeded') {
                    $btc_transaction = new BuyCoinHistory();
                    $btc_transaction->type = STRIPE;
                    $btc_transaction->address = 'N/A';
                    $btc_transaction->user_id = Auth::id();
                    $btc_transaction->doller = $coin_price_doller;
                    $btc_transaction->btc = $coin_price_btc;
                    $btc_transaction->phase_id = $phase_id;
                    $btc_transaction->referral_level = $referral_level;
                    $btc_transaction->fees  = $phase_fees ;
                    $btc_transaction->bonus = $bonus;
                    $btc_transaction->referral_bonus = $affiliation_percentage;
                    $btc_transaction->requested_amount = $coin_amount;
                    $btc_transaction->coin = $request->coin;
                    $btc_transaction->coin_type = "Default";
                    $btc_transaction->stripe_token = $charge['id'];
                    $btc_transaction->save();

//                    DB::beginTransaction();
//                    try {
//                        $affiliate_servcice = new AffiliateRepository();
//                        $transaction = BuyCoinHistory::where(['id' => $btc_transaction->id, 'status' => STATUS_PENDING])->firstOrFail();
//
//                        $primary = get_primary_wallet($transaction->user_id, 'Default');
//
//                        $primary->increment('balance', $transaction->coin);
//                        $transaction->status = STATUS_SUCCESS;
//                        $transaction->save();
//
//                        if (!empty($transaction->phase_id)) {
//                            $bonus = $affiliate_servcice->storeAffiliationHistoryForBuyCoin($transaction);
//                        }
//                    } catch (\Exception $e) {
//                        DB::rollBack();
//                        return redirect()->back()->with('dismiss', 'Something went wrong');
//                    }
//
//                    DB::commit();
                    return redirect()->back()->with('success', __("Request submitted successful,Please wait for admin approval"));
                } else {
                    return redirect()->back()->with('dismiss', "Payment failed");
                }

            }
        } else {
            return redirect()->back()->with('dismiss', "Something went wrong");
        }
    }

    //bank details
    public function bankDetails(Request $request)
    {
        $data = ['success' => false, 'message' => __('Invalid request'), 'data_genetare'=> ''];
        $data_genetare = '';
        if(isset($request->val)) {
            $bank = Bank::where('id', $request->val)->first();
            if (isset($bank)) {
                $data_genetare = '<h3 class="text-center">'.__('Bank Details').'</h3><table class="table">';
                $data_genetare .= '<tr><td>'.__("Bank Name").' :</td> <td>'.$bank->bank_name.'</td></tr>';
                $data_genetare .= '<tr><td>'.__("Account Holder Name").' :</td> <td>'.$bank->account_holder_name.'</td></tr>';
                $data_genetare .= '<tr><td>'.__("Bank Address").' :</td> <td>'.$bank->bank_address.'</td></tr>';
                $data_genetare .= '<tr><td>'.__("Country").' :</td> <td>'.country($bank->country).'</td></tr>';
                $data_genetare .= '<tr><td>'.__("IBAN").' :</td> <td>'.$bank->iban.'</td></tr>';
                $data_genetare .= '<tr><td>'.__("Swift Code").' :</td> <td>'.$bank->swift_code.'</td></tr>';
                $data_genetare .= '</table>';
                $data['data_genetare'] = $data_genetare;
                $data['success'] = true;
                $data['message'] = __('Data get successfully.');
            }
        }

        return response()->json($data);
    }

    // coin payment success page
    public function buyCoinByAddress($address)
    {
        $data['title'] = __('Coin Payment');
        if (is_numeric($address)) {
            $coinAddress = BuyCoinHistory::where(['user_id' => Auth::id(), 'id' => $address, 'status' => STATUS_PENDING])->first();
        } else {
            $coinAddress = BuyCoinHistory::where(['user_id' => Auth::id(), 'address' => $address, 'status' => STATUS_PENDING])->first();
        }
        if (isset($coinAddress)) {
            $data['coinAddress'] = $coinAddress;
            return view('user.buy_coin.payment_success', $data);
        } else {
            return redirect()->back()->with('dismiss', __('Address not found'));
        }
    }

    // buy coin history
    public function buyCoinHistory(Request $request)
    {
        $data['title'] = __('Buy Coin History');
        if ($request->ajax()) {
            $items = BuyCoinHistory::where(['user_id'=>Auth::id()]);
            return datatables($items)
                ->addColumn('type', function ($item) {
                    return byCoinType($item->type);
                })
                ->addColumn('status', function ($item) {
                    return deposit_status($item->status);
                })
                ->make(true);
        }

        return view('user.buy_coin.buy_history', $data);
    }

    // give or request coin
    public function requestCoin(Request $request)
    {
        $data['title'] = __('Request or Give Coin ');
        $data['wallets'] = Wallet::where(['user_id' => Auth::id(), 'coin_type' => 'Default'])->where('balance','>',0)->get();
        $data['qr'] = (!empty($request->qr)) ? $request->qr : 'requests';

        return view ('user.request_coin.coin_request', $data);
    }

    // send coin request
    public function sendCoinRequest(Request $request)
    {
        $rules = [
            'email' => 'required|exists:users,email',
            'amount' => ['required','numeric','min:'.settings("minimum_withdrawal_amount"),'max:'.settings('maximum_withdrawal_amount')]
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errors = [];
            $e = $validator->errors()->all();
            foreach ($e as $error) {
                $errors[] = $error;
            }
            $data['message'] = $errors;
            return redirect()->route('requestCoin', ['qr' => 'requests'])->with(['dismiss' => $errors[0]]);
        }

        try {
            $response = app(CoinRepository::class)->sendCoinAmountRequest($request);
            if ($response['success'] == true) {
                return redirect()->route('requestCoin', ['qr' => 'requests'])->with('success', $response['message']);
            } else {
                return redirect()->route('requestCoin', ['qr' => 'requests'])->withInput()->with('success', $response['message']);
            }
        } catch(\Exception $e) {
            return redirect()->back()->with(['dismiss' => $e->getMessage()]);
        }
    }

    // send coin request
    public function giveCoin(Request $request)
    {
        $rules = [
            'wallet_id' => 'required|exists:wallets,id',
            'amount' => ['required','numeric','min:'.settings("minimum_withdrawal_amount"),'max:'.settings('maximum_withdrawal_amount')],
            'email' => 'required|exists:users,email'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errors = [];
            $e = $validator->errors()->all();
            foreach ($e as $error) {
                $errors[] = $error;
            }
            $data['message'] = $errors;
            return redirect()->route('requestCoin', ['qr' => 'give'])->with(['dismiss' => $errors[0]]);
        }

        try {
            $response = app(CoinRepository::class)->giveCoinToUser($request);
            if ($response['success'] == true) {
                return redirect()->route('requestCoin', ['qr' => 'give'])->with('success', $response['message']);
            } else {
                return redirect()->route('requestCoin', ['qr' => 'give'])->withInput()->with('success', $response['message']);
            }
        } catch(\Exception $e) {
            return redirect()->back()->with(['dismiss' => $e->getMessage()]);
        }
    }

    // send coin history
    public function giveCoinHistory(Request $request)
    {
        $data['title'] = __('Send Coin History');
        if ($request->ajax()) {
            $items = CoinRequest::where(['sender_user_id'=>Auth::id()]);
            return datatables($items)
                ->editColumn('sender_user_id', function ($item) {
                    return $item->sender->email;
                })
                ->editColumn('coin_type', function ($item) {
                    return settings('coin_name');
                })
                ->editColumn('receiver_user_id', function ($item) {
                    return $item->receiver->email;
                })
                ->editColumn('status', function ($item) {
                    return deposit_status($item->status);
                })
                ->make(true);
        }

        return view('user.request_coin.coin_give_history', $data);
    }

    // send coin history
    public function receiveCoinHistory(Request $request)
    {
        $data['title'] = __('Received Coin History');
        if ($request->ajax()) {
            $items = CoinRequest::where(['receiver_user_id'=>Auth::id()]);
            return datatables($items)
                ->editColumn('sender_user_id', function ($item) {
                    return $item->sender->email;
                })
                ->editColumn('coin_type', function ($item) {
                    return settings('coin_name');;
                })
                ->editColumn('receiver_user_id', function ($item) {
                    return $item->receiver->email;
                })
                ->editColumn('status', function ($item) {
                    return deposit_status($item->status);
                })
                ->make(true);
        }

        return view('user.request_coin.coin_receive_history', $data);
    }

    // pending request coin history
    public function pendingRequest(Request $request)
    {
        $data['title'] = __('Pending Request');
        if ($request->ajax()) {
            $items = CoinRequest::where(['sender_user_id'=>Auth::id(), 'status'=> STATUS_PENDING]);
            return datatables($items)
                ->editColumn('receiver_user_id', function ($item) {
                    return $item->receiver->email;
                })
                ->editColumn('coin_type', function ($item) {
                    return settings('coin_name');;
                })
                ->addColumn('action', function ($wdrl) {
                    $action = '<ul>';
                    $action .= accept_html('acceptCoinRequest',encrypt($wdrl->id));
                    $action .= reject_html('declineCoinRequest',encrypt($wdrl->id));
                    $action .= '<ul>';
                    return $action;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('user.request_coin.coin_pending_request', $data);
    }

    // coin request accept process

    public function acceptCoinRequest($id)
    {
        try {
            $request_id = decrypt($id);
        } catch (\Exception $e) {
            return redirect()->back();
        }
        try {
            $response = app(CoinRepository::class)->acceptCoinRequest($request_id);
            if ($response['success'] == true) {
                return redirect()->back()->with('success', $response['message']);
            } else {
                return redirect()->back()->withInput()->with('success', $response['message']);
            }
        } catch(\Exception $e) {
            return redirect()->back()->with(['dismiss' => $e->getMessage()]);
        }
    }

    // pending coin reject process
    public function declineCoinRequest($id)
    {
        if (isset($id)) {
            try {
                $request_id = decrypt($id);
            } catch (\Exception $e) {
                return redirect()->back();
            }
            try {
                $response = app(CoinRepository::class)->rejectCoinRequest($request_id);
                if ($response['success'] == true) {
                    return redirect()->back()->with('success', $response['message']);
                } else {
                    return redirect()->back()->withInput()->with('success', $response['message']);
                }
            } catch(\Exception $e) {
                return redirect()->back()->with(['dismiss' => $e->getMessage()]);
            }

        }
    }

}
