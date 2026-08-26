<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Order;
use App\Http\Resources\PaymentResource;
use App\Models\Wallet;
use App\Models\WalletHistory;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Http\Resources\DeliveryManEarningResource;
use App\Models\AppSetting;
use App\Models\OrderHistory;
use App\Models\PaymentGateway;
use App\Models\PayTRPayment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function paymentSave(Request $request)
    {
        $data = $request->all();
       
        if (isset($data['payment_status'], $data['is_online']) && $data['payment_status'] == 'paid' && $data['is_online'] == true) {
            Order::where('id', $data['order_id'])->update(['status' => 'create']);
            
            if ($result = Order::find($data['order_id'])) {
                saveOrderHistory(['history_type' => $result->status, 'order_id' => $result->id, 'order' => $result]);
            }   
            $app_setting = AppSetting::first();
                    if ($app_setting && $app_setting->auto_assign == 1) {
                        $this->autoAssignOrder($result);
                    }         
        }        

        $data['datetime'] = isset($request->datetime) ? date('Y-m-d H:i:s',strtotime($request->datetime)) : date('Y-m-d H:i:s');

        if( request('payment_type') == 'wallet' ) {
            $wallet = Wallet::where('user_id', request('client_id'))->first();
            if($wallet != null) {
                if($wallet->total_amount < request('total_amount')) {
                    $message = __('message.balance_insufficient');
                    return json_message_response($message,400);
                }
                $data['payment_status'] = 'paid';
            } else {
                $message = __('message.not_found_entry',['name' => __('message.wallet')]);
                return json_message_response($message,400);
            }
        }

        try {
            DB::beginTransaction();
            if( !in_array(request('payment_type'), ['cash','wallet']) ) {
                $data['received_by'] = 'admin';
            }
            $result = Payment::updateOrCreate(['id' => $request->id],$data);
            if( $result->payment_status == 'paid') {
                if( $result->payment_type == 'wallet') {
                    $wallet->decrement('total_amount', $result->total_amount );
                    $order = $result->order;

                    $admin_id = User::admin()->id;
                    $currency = appSettingcurrency('currency_code');
                    $client_wallet = Wallet::where('user_id', $order->client_id)->first();
                    $client_wallet_history = [
                        'user_id'           => $order->client_id,
                        'type'              => 'debit',
                        'currency'          => $currency,
                        'transaction_type'  => 'order_fee',
                        'amount'            => $result->total_amount,
                        'balance'           => $client_wallet->total_amount,
                        'order_id'          => $result->order_id,
                        'datetime'          => date('Y-m-d H:i:s'),
                        'data' => [
                            'payment_id'    => $result->id,
                        ]
                    ];

                    WalletHistory::create($client_wallet_history);

                    $admin_wallet = Wallet::firstOrCreate(
                        [ 'user_id' => $admin_id ]
                    );
                    $admin_wallet->increment('total_amount', $result->total_amount );
                    $admin_wallet_history = [
                        'user_id'           => $admin_id,
                        'type'              => 'credit',
                        'currency'          => $currency,
                        'transaction_type'  => 'order_fee',
                        'amount'            => $result->total_amount,
                        'balance'           => $admin_wallet->total_amount,
                        'order_id'          => $result->order_id,
                        'datetime'          => date('Y-m-d H:i:s'),
                        'data' => [
                            'payment_id'    => $result->id,
                        ]
                    ];
                    WalletHistory::create($admin_wallet_history);
                }
            }
            DB::commit();
        } catch(\Exception $e) {
            // \Log::info($e);
            DB::rollBack();
            return json_custom_response($e);
        }

        $order = Order::find($request->order_id);
        $order->payment_id = $result->id;

        $order->save();

        $status_code = 200;
        if($result->payment_status == 'paid')
        {
            $message = __('message.payment_completed');
        } else {
            $message = __('message.payment_status_message',['status' => __('message.'.$result->payment_status), 'id' => $order->id  ]);
        }

        if($result->payment_status == 'failed')
        {
            $status_code = 400;
        }

        $history_data = [
            'history_type' => 'payment_status_message',
            'payment_status'=> $result->payment_status,
            'order_id' => $order->id,
            'order' => $order,
        ];

        saveOrderHistory($history_data);

        return json_message_response($message,$status_code);
    }

    public function getList(Request $request)
    {

        $payment = Payment::myPayment();

        $payment->when(request('delivery_man_id'), function ($query) {
            return $query->whereHas('order', function ($q) {
                $q->where('delivery_man_id',request('delivery_man_id'));
            });
        });

        $payment->when(request('type') == 'earning', function ($query) {
            return $query->whereHas('order', function ($q) {
                $q->whereIn('status',['completed', 'cancelled']);
            });
        });
        $payment->when(request('client_id'), function ($query) {
            $query->where('client_id',request('client_id'));
        });

        $per_page = config('constant.PER_PAGE_LIMIT');
        if( $request->has('per_page') && !empty($request->per_page)){

            if(is_numeric($request->per_page))
            {
                $per_page = $request->per_page;
            }

            if($request->per_page == -1 ){
                $per_page = $payment->count();
            }
        }

        $payment = $payment->orderBy('id','desc')->paginate($per_page);
        $items = PaymentResource::collection($payment);

        $response = [
            'pagination' => json_pagination_response($items),
            'data' => $items,
        ];

        return json_custom_response($response);
    }

    public function getDeliveryManEarningList(Request $request)
    {
        $delivery_earning = User::select('users.id','users.name')->where('user_type', 'delivery_man')->has('deliveryManOrder')
            ->with(['getPayment:order_id,delivery_man_commission,admin_commission', 'userWallet:total_amount,total_withdrawn'])
            ->withCount(['deliveryManOrder as total_order',
                    'getPayment as paid_order' => function ($query) {
                        $query->where('payment_status', 'paid');
                    }]
            )
            ->withSum('userWallet as wallet_balance', 'total_amount')
            ->withSum('userWallet as total_withdrawn', 'total_withdrawn')
            ->withSum('getPayment as delivery_man_commission', 'delivery_man_commission')
            ->withSum('getPayment as admin_commission', 'admin_commission');

        $per_page = config('constant.PER_PAGE_LIMIT');
        if( $request->has('per_page') && !empty($request->per_page))
        {
            if(is_numeric($request->per_page)){
                $per_page = $request->per_page;
            }
            if($request->per_page == -1 ){
                $per_page = $delivery_earning->count();
            }
        }

        $delivery_earning = $delivery_earning->orderBy('id','desc')->paginate($per_page);

        $items = DeliveryManEarningResource::collection($delivery_earning);

        $response = [
            'pagination' => json_pagination_response($items),
            'data' => $items,
        ];

        return json_custom_response($response);
    }

    public function initiatePayment(Request $request)
    {
        $payment_setting_data = PaymentGateway::where('type', 'Paytr')->first();
        $settings = $payment_setting_data->test_value;  
        
        $validated = $request->validate([
            'cc_owner' => 'required|string|max:50',
            'card_number' => 'required|string|max:16',
            'expiry_month' => 'required|string|size:2',
            'expiry_year' => 'required|string|size:2',
            'cvv' => 'required|string|max:4',
            'payment_amount' => 'required|numeric',
            'non_3d' => 'required|integer|in:0,1',
        ]);

        $merchant_id = $settings['merchant_id'];
        $merchant_key = $settings['merchant_key'];
        $merchant_salt = $settings['merchant_salt'];
        $merchant_oid = rand();
        $user_ip = request()->ip();
        

        $user_basket = htmlentities(json_encode(array(array("Logistics Service", $validated['payment_amount'], 1))));

        $hash_str = $merchant_id . $user_ip . $merchant_oid . auth()->user()->email . $validated['payment_amount'] . 'card' . 0 . 'TL' . $payment_setting_data->is_test. $validated['non_3d'];
        $paytr_token = base64_encode(hash_hmac('sha256', $hash_str . $merchant_salt, $merchant_key, true));

        $merchant_ok_url = route('paytr-success');
        $merchant_fail_url = route('paytr-failed');

        $payload = [
            'merchant_id' => $merchant_id,
            'user_ip' => $user_ip,
            'merchant_oid' => $merchant_oid,
            'email' => auth()->user()->email,
            'payment_amount' => $validated['payment_amount'],
            'payment_type' => 'card',
            'installment_count' => 0,
            'currency' => 'TL',
            'test_mode' => $payment_setting_data->is_test,
            'non_3d' => $validated['non_3d'],
            'cc_owner' => $validated['cc_owner'],
            'card_number' => $validated['card_number'],
            'expiry_month' => $validated['expiry_month'],
            'expiry_year' => $validated['expiry_year'],
            'cvv' => $validated['cvv'],
            'merchant_ok_url' => $merchant_ok_url,
            'merchant_fail_url' => $merchant_fail_url,
            'user_name' => auth()->user()->name,
            'user_address' => auth()->user()->address ?? null,
            'user_phone' => auth()->user()->contact_number ?? null,
            'user_basket' => $user_basket,
            'paytr_token' => $paytr_token,
            'debug_on' => env('APP_DEBUG') ? 1 : 0,
            'client_lang' => 'en'
        ];

        Log::info("data" .  $merchant_oid);

        try {
            $response = Http::asForm()->post('https://www.paytr.com/odeme', $payload);

            if ($response->successful()) {
                if($request['order_id']){
                    $payment =  Payment::create([
                        "order_id" =>  $request['order_id'],
                        "client_id" => auth()->user()->id,
                        "datetime" => now(),
                        "merchant_oid" => $merchant_oid,
                        'total_amount' => $validated['payment_amount'],
                        'payment_type' => 'paytr',
                        'payment_status'  => 'pending',
                    ]);
                    Order::where('id', $request['order_id'])->update(['payment_id' => $payment->id]);
                    $paymenttrp =  PayTRPayment::create([
                        'client_id' => auth()->user()->id,
                        'merchant_oid' => $merchant_oid,
                        'order_id' => $merchant_oid,
                        'merchant_oid' => $merchant_oid,
                        'merchant_id' => $settings['merchant_id'],
                        'hash' => $paytr_token,
                        'datetime' => now(),
                        'total_amount' => $validated['payment_amount'],
                        'payment_type' =>$validated['payment_type'] ?? "card",
                        'payment_status' => 'pending',
                    ]);
                }else{
                    $paymenttrp =  PayTRPayment::create([
                        'client_id' => auth()->user()->id,
                        'merchant_oid' => $merchant_oid,
                        'order_id' => $merchant_oid,
                        'merchant_oid' => $merchant_oid,
                        'merchant_id' => $settings['merchant_id'],
                        'hash' => $paytr_token,
                        'datetime' => now(),
                        'total_amount' => $validated['payment_amount'],
                        'payment_type' =>$validated['payment_type'] ?? "card",
                        'payment_status' => 'pending',
                    ]);
                }


                return response()->json(['status' => 'success', 'data' => $response->body()], 200);
            }

            return response()->json(['status' => 'error', 'response' => $response->body()], 400);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'error' => $e->getMessage()], 503);
        }
    }

    public function success()
    {
        return view('paytr.success');
    }

    public function fail()
    {
        return view('paytr.failed');
    }

    public function callback(Request $request)
    {
        $status = $request->input('status');
        $transactionId = $request->input('merchant_oid');
    
        $paymentModule = Payment::where('merchant_oid', $transactionId)->first();
        $payment = PayTRPayment::where('merchant_oid', $transactionId)->first();
    
        if ($status === 'success') {

            if ($paymentModule) {
                $paymentModule->update(['payment_status' => 'paid']);
            }
        
            if ($payment) {
                $payment->update(['payment_status' => 'paid']);
            } else {
                $payment = PayTRPayment::where('merchant_oid', $transactionId)->first();
                if ($payment) {
                    $payment->update(['payment_status' => 'paid']);
                }
            }
        
            if ($paymentModule) {
                $order = Order::find($paymentModule->order_id);
        
                if ($order) {
                    $order->update(['status' => 'create']);
        
                    $history_data = [
                        'client_id'   => $order->client_id,
                        'client_name' => optional($order->client)->name,
                    ];
        
                    OrderHistory::create([
                        'history_data'     => json_encode($history_data),
                        'order_id'         => $order->id,
                        'history_type'     => 'create',
                        'history_message'  => __('message.order_create'),
                    ]);
        
                    $app_setting = AppSetting::first();
                    if ($app_setting && $app_setting->auto_assign == 1) {
                        $this->autoAssignOrder($order);
                    }
                }
            }
        
            if ($payment && $payment->client_id) {
                $clientWallet = Wallet::where('user_id', $payment->client_id)->first();
                if ($clientWallet) {
                    $clientWallet->increment('total_amount', $payment->total_amount);
        
                    WalletHistory::create([
                        'user_id'          => $payment->client_id,
                        'type'             => 'credit',
                        'currency'         => 'TL',
                        'transaction_type' => 'topup',
                        'amount'           => $payment->total_amount,
                        'datetime'         => now(),
                        'data'             => ['payment_id' => $payment->id],
                    ]);
                }
            }
        
            return response('OK', 200);
        
        } else {
            if ($paymentModule) {
                Order::where('id', $paymentModule->order_id)->update(['status' => 'cancelled','reason' => __('message.payment_failed') ]);
        
                $data['order_id']        = $paymentModule->order_id;
                $data['history_type']    = 'cancelled';
                $data['history_message'] = __('message.cancelled_order');
                $data['history_data']    = json_encode([
                    'reason' => __('message.payment_failed'),
                    'status' => 'cancelled',
                ]);
        
                OrderHistory::create($data);
            }
        
            if ($payment) {
                $payment->update(['payment_status' => 'failed']);
            }
        
            return response('Failed', 400);
        }
    
        return response('OK', 200);
    }
}
