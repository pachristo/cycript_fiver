<?php

namespace App\Http\Controllers\user;

use App\Http\Requests\CoinSwapRequest;
use App\Http\Requests\WalletCreateRequest;
use App\Http\Requests\withDrawRequest;
use App\Http\Services\CommonService;
use App\Http\Services\TransactionService;
use App\Jobs\Withdrawal;
use App\Model\Coin;
use App\Model\CoWalletWithdrawApproval;
use App\Model\DepositeTransaction;
use App\Model\TempWithdraw;
use App\Model\Wallet;
use App\Model\WalletAddressHistory;
use App\Model\WalletCoUser;
use App\Model\WalletSwapHistory;
use App\Model\WithdrawHistory;
use App\Repository\WalletRepository;
use App\Services\BitCoinApiService;
use App\Services\CoinPaymentsAPI;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use PragmaRX\Google2FA\Google2FA;


class WalletController extends Controller
{
    public $repo;

    public function __construct()
    {
        $this->repo = new WalletRepository();
    }

    // my pocket
    public function myPocket(Request $request)
    {
        $data['tab'] = $request->tab ?? null;
        $data['wallets'] = Wallet::where(['user_id'=> Auth::id(), 'type'=> PERSONAL_WALLET])->orderBy('id', 'ASC')->get();
        $data['coWallets'] = Wallet::select('wallets.*')
            ->join('wallet_co_users', 'wallet_co_users.wallet_id','=','wallets.id')
            ->where(['wallets.type'=> CO_WALLET, 'wallet_co_users.user_id'=>Auth::id()])
            ->orderBy('id', 'ASC')->get();
        $data['coins'] = Coin::where('status', STATUS_ACTIVE)->get();
        $data['title'] = __('My Pocket');

        return view('user.pocket.index', $data);
    }

    public function getCoinSwapDetails(Request $request)
    {
        if ($request->ajax()) {
            $wallet = Wallet::find($request->id);
            $data['wallets'] = Coin::select('coins.*', 'wallets.name as wallet_name', 'wallets.id as wallet_id')
                ->join('wallets', 'wallets.coin_type', '=', 'coins.type')
                ->where('coins.status', STATUS_ACTIVE)
                ->where('wallets.user_id', Auth::id())
                ->where('coins.type', '!=', $wallet->coin_type)
                ->get();
            $html = '';
            $html .= View::make('user.pocket.swap_wallet_list', $data);

            return response()->json($html);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * get rate of coin
     */
    public function getRate(CoinSwapRequest $request)
    {
        $data = $this->repo->get_wallet_rate($request);

        $html = '';
        if ($data['success'] == true) {
            $html .= View::make('user.pocket.swap_data', $data);
        }

        return response()->json($html);
    }

    public function swapCoin(CoinSwapRequest $request)
    {
        $fromWallet = Wallet::where(['id'=>$request->from_coin_id])->first();
        if (!empty($fromWallet) && $fromWallet->type == CO_WALLET)
            return redirect()->back()->with(['dismiss' => __('Something went wrong')]);

        $response = $this->repo->get_wallet_rate($request);
        if ($response['success'] == false) {
            return redirect()->back()->with(['dismiss' => __('Something went wrong')]);
        }
        $swap_coin = $this->repo->coinSwap($response['from_wallet'], $response['to_wallet'], $response['convert_rate'], $response['amount'], $response['rate']);

        if ($swap_coin['success'] == true) {
            return redirect()->back()->with(['success' => $swap_coin['message']]);
        } else {
            return redirect()->back()->with(['dismiss' => $swap_coin['message']]);
        }
    }

    // make default account
    public function makeDefaultAccount($account_id, $coin_type)
    {
        $wallet = Wallet::where(['id'=>$account_id])->first();
        if (!empty($wallet) && $wallet->type == CO_WALLET)
            return redirect()->back()->with(['dismiss' => __('Something went wrong')]);

        Wallet::where(['user_id' => Auth::id(), 'coin_type' => $coin_type])->update(['is_primary' => 0]);
        Wallet::updateOrCreate(['id' => $account_id], ['is_primary' => 1]);

        return redirect()->back()->with('success', __('Default set successfully'));
    }

    // create new wallet
    public function createWallet(WalletCreateRequest $request)
    {
        if (!empty($request->wallet_name)) {
            try {
                DB::beginTransaction();
                $wallet = new Wallet();
                $wallet->user_id = Auth::id();
                $wallet->type = $request->type ?? PERSONAL_WALLET;
                $wallet->name = $request->wallet_name;
                $wallet->coin_type = strtoupper($request->coin_type);
                $wallet->status = STATUS_SUCCESS;
                $wallet->balance = 0;
                if (co_wallet_feature_active() && $request->type == CO_WALLET) {
                    $key = Str::random(64);
                    while (true) {
                        $keyExists = Wallet::where(['key' => $key])->first();
                        if (!empty($keyExists)) $key = Str::random(64);
                        else break;
                    }
                    $wallet->key = $key;
                }
                $wallet->save();

                if (co_wallet_feature_active() && $request->type == CO_WALLET) {
                    WalletCoUser::create([
                        'user_id' => Auth::id(),
                        'wallet_id' => $wallet->id
                    ]);
                }
                DB::commit();
                if (co_wallet_feature_active() && $request->type == CO_WALLET) {
                    return redirect()->route('myPocket', ['tab'=>'co-pocket'])->with('success', __("Pocket created successfully"));
                } else {
                    return redirect()->back()->with('success', __("Pocket created successfully"));
                }
            } catch (\Exception $e) {
                Log::alert($e->getMessage());
                DB::rollBack();
                return redirect()->back()->with('dismiss', __("Something went wrong."));
            }
        }
        return redirect()->back()->with('dismiss', __("Pocket name can't be empty"));
    }

    // create new wallet
    public function importWallet(Request $request)
    {
        if (!empty($request->key)) {
            $wallet = Wallet::where(['key' => $request->key, 'status' => STATUS_ACTIVE])->first();
            if (empty($wallet)) return back()->with('dismiss', __('Invalid Key'));

            $alreadyCoUser = WalletCoUser::where(['user_id'=>Auth::id(), 'wallet_id'=>$wallet->id])->first();
            if(!empty($alreadyCoUser)) return back()->with('dismiss', __('Already imported'));

            $maxCoUser = settings(MAX_CO_WALLET_USER_SLUG);
            $maxCoUser = !empty($maxCoUser) ? $maxCoUser : 2;
            $coUserCount = WalletCoUser::where(['wallet_id' => $wallet->id])->count();
            if($coUserCount >= $maxCoUser) return redirect()->back()->with('dismiss', __("Can't import this pocket. Max co user limit reached."));

            try {
                WalletCoUser::create([
                    'user_id' => Auth::id(),
                    'wallet_id' => $wallet->id
                ]);
            } catch (\Exception $e) {
                Log::alert($e->getMessage());
                return redirect()->back()->with('dismiss', __("Something went wrong."));
            }

            return redirect()->route('myPocket', ['tab'=>'co-pocket'])->with('success', __("Co Pocket imported successfully"));
        }
        return redirect()->back()->with('dismiss', __("Key can't be empty"));
    }

    // wallet details
    public function walletDetails(Request $request, $id)
    {
        $data['wallet_id'] = $id;
        $data['wallet'] = Wallet::where(['id'=>$id, 'user_id'=>Auth::id()])->first();
        //checking if co-wallet
        if(co_wallet_feature_active() && empty($data['wallet'])) {
            $data['wallet'] = Wallet::select('wallets.*')
                ->join('wallet_co_users', 'wallet_co_users.wallet_id', '=', 'wallets.id')
                ->where(['wallets.id' => $id, 'wallets.type' => CO_WALLET, 'wallet_co_users.user_id' => Auth::id()])
                ->first();

            $data['ac_tab'] = $request->has('ac_tab') ? $request->ac_tab : null;
        }
        if(empty($data['wallet'])) return back();
        if(co_wallet_feature_active()) $data['tempWithdraws'] = TempWithdraw::where(['wallet_id'=>$id, 'status'=>STATUS_PENDING])->orderBy('id','desc')->get();

        $exists = WalletAddressHistory::where('wallet_id',$id)->orderBy('created_at','desc')->first();
        $data['address'] = (!empty($exists)) ? $exists->address : get_coin_payment_address($data['wallet']->coin_type);
        $data['histories'] = DepositeTransaction::where('receiver_wallet_id', $id)->orderBy('id','desc')->get();
        $data['withdraws'] = WithdrawHistory::where('wallet_id', $id)->orderBy('id','desc')->get();
        $data['active'] = $request->q;
        $data['title'] = $request->q;
        if (!empty($data['address'])) {
            if (empty($exists)) {
                $history = new \App\Services\wallet();
                $history->AddWalletAddressHistory($id, $data['address'], $data['wallet']->coin_type);
            }
            $data['address_histories'] = WalletAddressHistory::where('wallet_id', $id)->paginate(10);

            return view('user.pocket.wallet_details', $data);
        }


        return redirect()->back()->with('dismiss', __('Wallet address not found. Please talk to admin about coin payment credential'));
    }

    // generate new wallet address
    public function generateNewAddress(Request $request)
    {
        try {
            $wallet = new \App\Services\wallet();
            $myWallet = Wallet::find($request->wallet_id);
            $address = get_coin_payment_address($myWallet->coin_type);

            if (!empty($address)) {
                $wallet->AddWalletAddressHistory($request->wallet_id, $address, $myWallet->coin_type);
                return redirect()->back()->with(['success' => __('Address generated successfully')]);
            } else {
                return redirect()->back()->with(['dismiss' => __('Address not generated ')]);
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('dismiss', $e->getMessage());
        }

    }

    // generate qr code
    public function qrCodeGenerate(Request $request)
    {
        $image = QRCode::text($request->address)->png();
        return response($image)->header('Content-type', 'image/png');
    }

    // withdraw balance
    public function WithdrawBalance(withDrawRequest $request) {
        $wallet = Wallet::where(['id'=>$request->wallet_id, 'user_id'=>Auth::id()])->first();

        //checking if co-wallet
        if(co_wallet_feature_active() && empty($wallet)) {
            $wallet = Wallet::select('wallets.*')
                ->join('wallet_co_users', 'wallet_co_users.wallet_id', '=', 'wallets.id')
                ->where(['wallets.id' => $request->wallet_id, 'wallets.type' => CO_WALLET, 'wallet_co_users.user_id' => Auth::id()])
                ->first();
        }

        if(empty($wallet)) return response()->json(['success'=>false,'message'=> __('Pocket not found.')]);
        $NodeDetails = new \App\Services\wallet();

        $address = $request->address;
        $user = Auth::user();
        if ($request->ajax()) {
            if ($wallet->balance >= $request->amount) {

                if (filter_var($address, FILTER_VALIDATE_EMAIL)) {
                    $receiverUser = User::where('email', $address)->first();
                    if (empty($receiverUser)) {
                        return response()->json(['success' => false, 'message' => __('Not a valid email address to send amount!')]);
                    }
                    if ($user->id == $receiverUser->id) {
                        return response()->json(['success' => false, 'message' => __('You can\'t send to your own wallet!')]);
                    }
                    $fees = 0;

                } else {
                    $walletAddress = $this->isInternalAddress($address);
                    if ($walletAddress) {

                        $fees = 0;
                        $receiverUser = $walletAddress->wallet->user;
                        if ($user->id == $receiverUser->id) {
                            return response()->json(['success' => false, 'message' => __('You can\'t send to your own wallet!')]);
                        }
                        if ($wallet->coin_type != $walletAddress->wallet->coin_type) {
                            return response()->json(['success' => false, 'message' => __('You can\'t make withdrawal, because wallet coin type is mismatched. Your wallet coin type and withdrawal address coin type should be same.')]);
                        }
                    } else {
                        $fees = calculate_fees($request->amount, settings('send_fees_type'));
                        if ($wallet->balance < ($request->amount + $fees)) {
                            return response()->json(['success' => false, 'message' => __('Wallet has no enough balance')]);
                        }
                    }
                }

                if (($request->amount + $fees) < settings('minimum_withdrawal_amount')) {
                    $message = __('Minimum withdrawal amount ') . settings('minimum_withdrawal_amount') . ' ' . settings('coin_name');
                    return response()->json(['success' => false, 'message' => $message]);
                }
                if (($request->amount + $fees) > settings('maximum_withdrawal_amount')) {
                    $message = __('Maximum withdrawal amount ') . settings('maximum_withdrawal_amount') . ' ' . settings('coin_name');
                    return response()->json(['success' => false, 'message' => $message]);
                }
                return response()->json(['success' => true]);
//                    return response()->json(['success'=>false,'message'=>__('Address is not valid')]);

            } else {
                return response()->json(['success' => false, 'message' => __('Wallet has no enough balance')]);
            }

        } else {
            if (filter_var($address, FILTER_VALIDATE_EMAIL)) {

                $receiverUser = User::where('email', $address)->first();

                if (empty($receiverUser)) {
                    return redirect()->back()->with('dismiss', __('Not a valid email address to send amount!'));
                }
                if ($user->id == $receiverUser->id) {
                    return redirect()->back()->with('dismiss', __('You can\'t send to your own wallet!'));
                }
                $fees = 0;

            } else {
                $walletAddress = $this->isInternalAddress($address);
                if ($walletAddress) {
                    $fees = 0;
                    $receiverUser = $walletAddress->wallet->user;
                    if ($user->id == $receiverUser->id) {
                        return redirect()->back()->with('dismiss', __('You can\'t send to your own wallet!'));
                    }
                    if ($wallet->coin_type != $walletAddress->wallet->coin_type) {
                        return redirect()->back()->with('dismiss', __('You can\'t make withdrawal, because wallet coin type is mismatched. Your wallet coin type and withdrawal address coin type should be same.'));
                    }
                } else {

                    $fees = calculate_fees($request->amount, settings('send_fees_type'));
                    if ($wallet->balance < ($request->amount + $fees)) {
                        return response()->json(['success' => false, 'message' => __('Wallet has no enough balance')]);
                    }
                }
            }
            if (($request->amount + $fees) < settings('minimum_withdrawal_amount')) {
                $message = __('Minimum withdrawal amount ') . settings('minimum_withdrawal_amount') . ' ' . settings('coin_name');
                return response()->json(['success' => false, 'message' => $message]);
            }
            if (($request->amount + $fees) > settings('maximum_withdrawal_amount')) {
                $message = __('Maximum withdrawal amount ') . settings('maximum_withdrawal_amount') . ' ' . settings('coin_name');
                return response()->json(['success' => false, 'message' => $message]);
            }

            $google2fa = new Google2FA();
            if (empty($request->code)) {
                return redirect()->back()->with('dismiss', __('Verify code is required'));
            }
            $valid = $google2fa->verifyKey($user->google2fa_secret, $request->code);

            $data = $request->all();
            $data['user_id'] = Auth::id();
            $request = new Request();
            $request = $request->merge($data);

            if ($valid) {
                if ($wallet->balance >= $request->amount) {
//                    $request =$request->all();

//                    $trans = new TransactionService();
//                    $response = $trans->send($request['wallet_id'],$request['address'],$request['amount'],'','',$request['user_id'],$request['message']);
//                    return $response;
                    try {
                        if ($wallet->type == PERSONAL_WALLET) {
                            dispatch(new Withdrawal($request->all()))->onQueue('withdrawal');
                            return redirect()->back()->with('success', __('Withdrawal placed successfully'));

                        } else if (co_wallet_feature_active() && $wallet->type == CO_WALLET) {
                            DB::beginTransaction();
                            $tempWithdraw = TempWithdraw::create([
                                'user_id' => $user->id,
                                'wallet_id' => $wallet->id,
                                'amount' => $request->amount,
                                'address' => $request->address,
                                'message' => $request->message
                            ]);

                            CoWalletWithdrawApproval::create([
                                'temp_withdraw_id' => $tempWithdraw->id,
                                'wallet_id' => $wallet->id,
                                'user_id' => $user->id
                            ]);
                            DB::commit();

                            if ((new TransactionService())->isAllApprovalDoneForCoWalletWithdraw($tempWithdraw)['success']) {
                                dispatch(new Withdrawal($tempWithdraw->toArray()))->onQueue('withdrawal');
                                return redirect()->back()->with('success', __('Withdrawal placed successfully'));
                            }
                            return redirect()->back()->with('success', __('Process successful. Need other co users approval.'));
                        } else {
                            return redirect()->back()->with('dismiss', __('Invalid Pocket type.'));
                        }

                    } catch (\Exception $e) {
                        DB::rollBack();
                        Log::error($e->getMessage());
                        return redirect()->back()->with('dismiss', __('Something went wrong.'));
                    }
                } else
                    return redirect()->back()->with('dismiss', __('Wallet has no enough balance'));
            } else
                return redirect()->back()->with('dismiss', __('Google two factor authentication is invalid'));
        }
    }

    //check internal address
    private function isInternalAddress($address)
    {
        return WalletAddressHistory::where('address', $address)->with('wallet')->first();
    }

    // transaction history
    public function transactionHistories(Request $request)
    {
        if ($request->ajax()) {
            $tr = new TransactionService();
            if ($request->type == 'deposit') {
                $histories = $tr->depositTransactionHistories(Auth::id())->get();
            } else {
                $histories = $tr->withdrawTransactionHistories(Auth::id())->get();
            }
            return datatables($histories)
                ->addColumn('address', function ($item) {
                    return $item->address;
                })
                ->addColumn('amount', function ($item) {
                    return $item->amount;
                })
                ->addColumn('hashKey', function ($item) use ($request) {
                    if ($request->type == 'deposit')
                        return (!empty($item)) ? $item->transaction_id : '';
                    else
                        return (!empty($item)) ? $item->transaction_hash : '';
                })
                ->addColumn('status', function ($item) {
                    return statusAction($item->status);
                })
                ->rawColumns(['user'])
                ->make(true);
        }
    }

    // withdraw rate
    public function withdrawCoinRate(Request $request)
    {
        if ($request->ajax()) {
            $data['amount'] = isset($request->amount) ? $request->amount : 0;
            $wallet = Wallet::find($request->wallet_id);
            $data['coin_type'] = $wallet->coin_type;

            $data['coin_price'] = bcmul(settings('coin_price'), $request->amount, 8);
            $coinpayment = new CoinPaymentsAPI();
            $api_rate = $coinpayment->GetRates('');

            $data['btc_dlr'] = converts_currency($data['coin_price'], $data['coin_type'], $api_rate);
            $data['btc_dlr'] = custom_number_format($data['btc_dlr']);

            return response()->json($data);
        }
    }

    // coin swap history
    public function coinSwapHistory(Request $request)
    {
        if ($request->ajax()) {
            $list = WalletSwapHistory::where(['user_id' => Auth::id()])->get();

            return datatables($list)
                ->editColumn('from_wallet_id', function ($item) {
                    return $item->fromWallet->name;
                })
                ->editColumn('to_wallet_id', function ($item) {
                    return $item->toWallet->name;
                })
                ->editColumn('requested_amount', function ($item) {
                    return $item->requested_amount . ' ' . check_default_coin_type($item->from_coin_type);
                })
                ->editColumn('converted_amount', function ($item) {
                    return $item->converted_amount . ' ' . check_default_coin_type($item->to_coin_type);
                })
                ->make(true);
        }

        return view('user.pocket.swap_history');
    }

    // coin swap
    public function coinSwap()
    {
        $data['title'] = __('Coin Swap');
        $data['wallets'] = Wallet::where('user_id', Auth::id())->get();

        return view('user.pocket.coin_swap', $data);
    }


    //co wallet users
    public function coWalletUsers(Request $request) {
        $data['title'] = __('Co Pocket Users');
        $data['wallet'] = Wallet::select('wallets.*')
            ->join('wallet_co_users', 'wallet_co_users.wallet_id','=','wallets.id')
            ->where(['wallets.id'=>$request->id, 'wallets.type'=> CO_WALLET, 'wallet_co_users.user_id'=>Auth::id()])
            ->first();
        if(empty($data['wallet'])) return back();

        $data['co_users'] = $data['wallet']->co_users;
        return view('user.pocket.co_users', $data);
    }

    //co wallet withdraw approval list
    public function coWalletApprovals(Request $request) {
        $data['title'] = __('Withdraw Approvals');
        $data['tempWithdraw'] = TempWithdraw::where(['status'=>STATUS_PENDING, 'id'=>$request->id])->first();
        if(empty($data['tempWithdraw'])) return redirect()->route('myPocket', ['tab'=>'co-pocket']);
        $response = (new TransactionService())->approvalCounts($data['tempWithdraw']);
        $data['total_required_approval'] = $response['requiredUserApprovalCount'];
        $data['approved_count'] = $response['alreadyApprovedUserCount'];
        $data['wallet'] = Wallet::select('wallets.*')
            ->join('wallet_co_users', 'wallet_co_users.wallet_id','=','wallets.id')
            ->where(['wallets.id'=>$data['tempWithdraw']->wallet_id, 'wallets.type'=> CO_WALLET, 'wallet_co_users.user_id'=>Auth::id()])
            ->first();
        if(empty($data['wallet'])) return redirect()->route('myPocket', ['tab'=>'co-pocket']);

        $data['co_users'] = WalletCoUser::select(DB::raw('wallet_co_users.*,
                            (CASE WHEN wallet_co_users.user_id=co_wallet_withdraw_approvals.user_id THEN '
                                .STATUS_ACCEPTED.' ELSE '.STATUS_PENDING.' END) approved'))
                            ->leftJoin('co_wallet_withdraw_approvals', function ($join) use ($data) {
                                $join->on('wallet_co_users.wallet_id', '=', 'co_wallet_withdraw_approvals.wallet_id')
                                ->on('wallet_co_users.user_id', '=', 'co_wallet_withdraw_approvals.user_id')
                                ->on('co_wallet_withdraw_approvals.temp_withdraw_id','=', DB::raw($data['tempWithdraw']->id));
                            })
                            ->where('wallet_co_users.wallet_id', $data['wallet']->id)
                            ->get();
        return view('user.pocket.co_approvals', $data);
    }

    //approve co wallet withdraw
    public function approveCoWalletWithdraw(Request $request) {
        $tempWithdraw = TempWithdraw::where(['status'=>STATUS_PENDING, 'id'=>$request->id])->first();
        if(empty($tempWithdraw)) return redirect()->route('myPocket', ['tab'=>'co-pocket'])->with('dismiss', __('Invalid withdrawal.'));

        $userAlreadyApproved = CoWalletWithdrawApproval::where(['temp_withdraw_id'=>$tempWithdraw->id, 'user_id'=>Auth::id()])->first();
        if(!empty($userAlreadyApproved)) return redirect()->route('walletDetails', [$tempWithdraw->wallet_id, 'q'=> 'activity', 'ac_tab'=>'co-withdraw'])
            ->with('dismiss', __('You already approved.'));

        $wallet = Wallet::select('wallets.*')
            ->join('wallet_co_users', 'wallet_co_users.wallet_id','=','wallets.id')
            ->where(['wallets.id'=>$tempWithdraw->wallet_id, 'wallets.type'=> CO_WALLET, 'wallet_co_users.user_id'=>Auth::id()])
            ->first();
        if(empty($wallet)) return redirect()->route('walletDetails', [$tempWithdraw->wallet_id, 'q'=> 'activity', 'ac_tab'=>'co-withdraw'])
            ->with('dismiss', __('Invalid pocket.'));

        try {
            CoWalletWithdrawApproval::create([
                'temp_withdraw_id' => $tempWithdraw->id,
                'wallet_id' => $wallet->id,
                'user_id' => Auth::id()
            ]);

            if ((new TransactionService())->isAllApprovalDoneForCoWalletWithdraw($tempWithdraw)['success']) {
                dispatch(new Withdrawal($tempWithdraw->toArray()))->onQueue('withdrawal');
                $message = __('All approval done and withdrawal placed successfully.');
                return redirect()->route('myPocket', ['tab'=>'co-pocket'])->with('success', $message);
            } else {
                $message = __('Approved successfully.');
                return back()->with('success', $message);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->route('walletDetails', [$tempWithdraw->wallet_id, 'q'=> 'activity', 'ac_tab'=>'co-withdraw'])
                ->with('dismiss', __('Something went wrong.'));
        }

    }

    //reject co wallet withdraw by withdraw requester
    public function rejectCoWalletWithdraw(Request $request) {
        $tempWithdraw = TempWithdraw::where(['status'=>STATUS_PENDING, 'id'=>$request->id, 'user_id'=> Auth::id()])->first();
        if(empty($tempWithdraw)) return redirect()->route('myPocket', ['tab'=>'co-pocket'])->with('dismiss', __('Invalid withdrawal.'));

        try {
            $tempWithdraw->status = STATUS_REJECTED;
            $tempWithdraw->save();
            return redirect()->route('myPocket', ['tab'=>'co-pocket'])->with('success', __('Withdraw rejected successfully.'));
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->route('walletDetails', [$tempWithdraw->wallet_id, 'q'=> 'activity', 'ac_tab'=>'co-withdraw'])
                ->with('dismiss', __('Something went wrong.'));
        }

    }

}
