<?php
/**
 * Created by PhpStorm.
 * User: rana
 * Date: 8/30/17
 * Time: 5:19 PM
 */

namespace App\Repository;

use App\Http\Services\CommonService;
use App\Jobs\GiveCoin;
use App\Model\CoinRequest;
use App\Model\Wallet;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class CoinRepository
{
    //create wallet
    public function buyCoin($request)
    {
        $response = ['success' => false, 'message' => __('Invalid request')];
        try {
            $url = file_get_contents('https://min-api.cryptocompare.com/data/price?fsym=USD&tsyms=BTC');

            if (isset(\GuzzleHttp\json_decode($url, true)['BTC'])) {
                $coin_price_doller = bcmul($request->coin, settings('coin_price'),8);
                $coin_price_btc = bcmul(\GuzzleHttp\json_decode($url, true)['BTC'], $coin_price_doller,8);
                $coin_price_btc = number_format($coin_price_btc, 8);

                if ($request->payment_type == BTC) {
                    $btc_transaction = new BuyCoinHistories();
                    $btc_transaction->address = settings('admin_coin_address');
                    $btc_transaction->type = BTC;
                    $btc_transaction->user_id = Auth::id();
                    $btc_transaction->coin = $request->coin;
                    $btc_transaction->doller = $coin_price_doller;
                    $btc_transaction->btc = $coin_price_btc;
                    $btc_transaction->save();

                    $response = ['success' => true, 'message' => __('Request submitted successful,please send BTC with this address')];
                } elseif ($request->payment_type == CARD) {
                    $common_servie = new CommonService();
                    $all_req = $request->all();
                    $all_req['btn_amount'] = $coin_price_btc;
                    $all_req['total_coin_price_in_dollar'] = $coin_price_doller;
                    $trans = $common_servie->make_transaction((object)$all_req);
                    if ($trans['success']) {
                        DB::beginTransaction();
                        try {
                            $btc_transaction = new BuyCoinHistories();
                            $btc_transaction->type = CARD;
                            $btc_transaction->user_id = Auth::id();
                            $btc_transaction->coin = $request->coin;
                            $btc_transaction->address = $trans['data']->networkTransactionId;
                            $btc_transaction->doller = $coin_price_doller;
                            $btc_transaction->btc = $coin_price_btc;
                            $btc_transaction->status = STATUS_SUCCESS;
                            $btc_transaction->save();

                            //  add  coin on balance //
                            $default_wallet = Wallet::where('user_id', Auth::id())->where('is_primary', 1)->first();
                            $default_wallet->balance = $default_wallet->balance + $request->coin;
                            $default_wallet->save();

                            DB::commit();
                            $response = ['success' => true, 'message' => __('Coin purchased successfully')];

                            // all good
                        } catch (\Exception $e) {

                            DB::rollback();
                            $response = ['success' => false, 'message' => __('Something went wrong')];
                            return $response;
                        }
                    } else {
                        $response = ['success' => false, 'message' => $trans['message']];
                    }
                } elseif ($request->payment_type = BANK_DEPOSIT) {
                    $btc_transaction = new BuyCoinHistories();
                    $btc_transaction->type = BANK_DEPOSIT;
                    $btc_transaction->address = 'N/A';
                    $btc_transaction->user_id = Auth::id();
                    $btc_transaction->doller = $coin_price_doller;
                    $btc_transaction->btc = $coin_price_btc;
                    $btc_transaction->coin = $request->coin;
                    $btc_transaction->bank_id = $request->bank_id;
                    $btc_transaction->bank_sleep = uploadFile($request->file('sleep'), IMG_SLEEP_PATH);
                    $btc_transaction->save();

                    $response = ['success' => true, 'message' => __('Request submitted successful,Please wait for admin approval')];
                }
            } else {
                $response = ['success' => false, 'message' => __('Invalid request')];
            }

        } catch(\Exception $e) {
            $response = ['success' => false, 'message' => __('Something went wrong')];
            return $response;
        }

        return $response;
    }

    // send coin amount request to user
    public function sendCoinAmountRequest($request)
    {
        try {
            $user = User::where(['email'=> $request->email, 'role'=> USER_ROLE_USER, 'status'=> STATUS_ACTIVE])->first();
            if (isset($user)) {
                if ($user->email == Auth::user()->email) {
                    $response = ['success' => false, 'message' => __('You can not send request to your own email')];
                    return $response;
                }
                $myWallet = get_primary_wallet(Auth::id(), 'Default');
                $userWallet = get_primary_wallet($user->id, 'Default');
                $data = [
                    'amount' => $request->amount,
                    'sender_user_id' => $user->id,
                    'sender_wallet_id' => $userWallet->id,
                    'receiver_user_id' => Auth::id(),
                    'receiver_wallet_id' => $myWallet->id
                ];
                CoinRequest::create($data);

                $response = ['success' => true, 'message' => __('Request sent successfully. Please wait for user approval')];
            } else {
                $response = ['success' => false, 'message' => __('User not found')];
            }

        } catch (\Exception $e) {
            $response = ['success' => false, 'message' => __('Something went wrong')];
            return $response;
        }

        return $response;
    }

    // give coin amount request to user
    public function giveCoinToUser($request)
    {
        try {
            $user = User::where(['email'=> $request->email, 'role'=> USER_ROLE_USER, 'status'=> STATUS_ACTIVE])->first();
            if (isset($user)) {
                if ($user->email == Auth::user()->email) {
                    $response = ['success' => false, 'message' => __('You can not give coin to your own email')];
                    return $response;
                }
                $myWallet = Wallet::where(['id' => $request->wallet_id])->first();
                if (isset($myWallet)) {
                    $userWallet = get_primary_wallet($user->id, $myWallet->coin_type);;
                    if ($myWallet->balance < $request->amount) {
                        $response = ['success' => false, 'message' => __('Your selected wallet has not enough coin to give')];
                        return $response;
                    }
                    $data = [
                        'amount' => $request->amount,
                        'receiver_wallet_id' => $userWallet->id,
                        'sender_wallet_id' => $myWallet->id,
                        'receiver_user_id' => $user->id,
                        'sender_user_id' => Auth::id(),
                        'update_id' => ''
                    ];

//                    $this->sendCoinToUser($data);
                    dispatch(new GiveCoin($data))->onQueue('give-coin');

                    $response = ['success' => true, 'message' => __('Coin sent successfully.')];
                } else {
                    $response = ['success' => false, 'message' => __('Wallet not found')];
                }

            } else {
                $response = ['success' => false, 'message' => __('User not found')];
            }

        } catch (\Exception $e) {
            $response = ['success' => false, 'message' => __('Something went wrong')];
            return $response;
        }

        return $response;
    }

    // accept coin request process
    public function acceptCoinRequest($request_id)
    {
        try {
            $request_coin = CoinRequest::where(['id' => $request_id, 'status'=> STATUS_PENDING])->first();

            if (isset($request_coin)) {
                $user = User::where(['id'=> $request_coin->sender_user_id])->first();

                $myWallet = Wallet::where(['id' => $request_coin->sender_wallet_id])->first();
                $userWallet = Wallet::where(['id' => $request_coin->receiver_wallet_id])->first();
                if (isset($myWallet)) {
                    if ($myWallet->balance < $request_coin->amount) {
                        $response = ['success' => false, 'message' => __('Your wallet has not enough coin to give')];
                        return $response;
                    }
                    $data = [
                        'amount' => $request_coin->amount,
                        'receiver_wallet_id' => $request_coin->receiver_wallet_id,
                        'sender_wallet_id' => $request_coin->sender_wallet_id,
                        'receiver_user_id' => $request_coin->receiver_user_id,
                        'sender_user_id' => $request_coin->sender_user_id,
                        'update_id' => $request_coin->id
                    ];

//                    $this->sendCoinToUser($data);
                    dispatch(new GiveCoin($data))->onQueue('give-coin');

                    $response = ['success' => true, 'message' => __('Coin request accepted successfully.')];
                } else {
                    $response = ['success' => false, 'message' => __('Wallet not found')];
                }

            } else {
                $response = ['success' => false, 'message' => __('Request not found')];
            }

        } catch (\Exception $e) {
            $response = ['success' => false, 'message' => __('Something went wrong')];
            return $response;
        }

        return $response;
    }
//  coin request rejected process
    public function rejectCoinRequest($request_id)
    {
        try {
            $request_coin = CoinRequest::where(['id' => $request_id, 'status'=> STATUS_PENDING])->first();

            if (isset($request_coin)) {
                $request_coin->update(['status'=> STATUS_REJECTED]);

                $response = ['success' => true, 'message' => __('Coin request rejected successfully.')];
            } else {
                $response = ['success' => false, 'message' => __('Request not found')];
            }

        } catch (\Exception $e) {
            $response = ['success' => false, 'message' => __('Something went wrong')];
            return $response;
        }

        return $response;
    }

    // send coin to user
    public function sendCoinToUser($data)
    {
        DB::beginTransaction();
        try {
            Log::info('give coin process started');
            $myWallet = Wallet::where(['id' => $data['sender_wallet_id']])->first();
            $userWallet = Wallet::where(['id' => $data['receiver_wallet_id']])->first();
            if ($myWallet->balance < $data['amount']) {
                $response = ['success' => false, 'message' => __('Your selected wallet has not enough coin to give')];
                Log::info('Your selected wallet has not enough coin to give');
                Log::info('give coin process failed');

                return $response;
            }
            if (!empty($data['update_id'])) {
                CoinRequest::where('id',$data['update_id'])->update(['status' => STATUS_SUCCESS]);
                Log::info('give coin = '.$data['amount']);
                Log::info('sender wallet id = '.$data['sender_wallet_id']);
                Log::info('receiver wallet id = '.$data['receiver_wallet_id']);
                $myWallet->decrement('balance',$data['amount']);
                $userWallet->increment('balance',$data['amount']);
            } else {
                $save = CoinRequest::create($data);
                if ($save) {
                    CoinRequest::where('id',$save->id)->update(['status' => STATUS_SUCCESS]);
                    Log::info('give coin = '.$data['amount']);
                    Log::info('sender wallet id = '.$data['sender_wallet_id']);
                    Log::info('receiver wallet id = '.$data['receiver_wallet_id']);
                    $myWallet->decrement('balance',$data['amount']);
                    $userWallet->increment('balance',$data['amount']);
                }
            }

            DB::commit();

            Log::info('give coin process success');
            $response = ['success' => true, 'message' => __('Coin sent successfully.')];

        } catch (\Exception $e) {
            Log::info('give coin process exception');
            Log::info($e->getMessage());
            DB::rollBack();
            $response = ['success' => false, 'message' => __('Something went wrong')];
            return $response;
        }
    }

}
