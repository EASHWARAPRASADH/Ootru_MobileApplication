<?php

namespace App\Traits;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
use App\Models\Payment;
use App\Models\Wallet;
use App\Models\WalletHistory;

use Illuminate\Support\Facades\DB;

trait PaymentTrait {

    public function walletTransactionCancelled($order_id)
    {
        $order = Order::where('id', $order_id)->first();
        if( $order == null ) {
            return false;
        }

        $payment = Payment::where('order_id',$order_id)->first();
        if( $payment == null ) {
            $payment = Payment::create([
                'order_id'      => $order->id,
                'client_id'     => $order->client_id,
                'datetime'      => date('Y-m-d H:i:s'),
                'payment_type'  => 'cash',
                'payment_status' => 'pending',
                'received_by'   => 'admin',
                'total_amount'  => $order->total_amount,
            ]);
        }
        $admin_id = User::admin()->id;
        $order_amount = $payment->total_amount;

        $order_history = $order->orderHistory()->whereIn('history_type', [ 'courier_picked_up', 'courier_departed'])->count();

        $cancel_charges = $order->city->cancel_charges ?? 0;
        $commission_type = $order->city->commission_type ?? 0;
        $admin_commission = $order->city->admin_commission ?? 0;
        $currency = appSettingcurrency('currency_code');

        if($commission_type == 'fixed') {
            if( $order_amount <= $admin_commission) {
                $order_amount += $admin_commission;
            }
        } else {
            if( $order_history > 0 ) {
                $admin_commission = $admin_commission ? ( $order_amount / 100) * $admin_commission : 0;
            }
        }
        if( $order_history > 0 ) {
            $order_cancel_refund = 0;
            $cancel_charges = $order_amount;

            $delivery_man_fee = $order_amount - $admin_commission;
            $payment->delivery_man_fee = $delivery_man_fee;
            $payment->delivery_man_commission = $delivery_man_fee + $payment->delivery_man_tip;
        } else {
            $order_cancel_refund = $order_amount - $cancel_charges;
            $admin_commission = $cancel_charges;
        }

        $client_wallet = Wallet::firstOrCreate(
            [ 'user_id' => $order->client_id ]
        );

        if( $payment->payment_type == 'cash') {
            $payment->received_by = 'delivery_man';
        } elseif ($payment->payment_type == 'wallet') {
            $payment->received_by = 'wallet';
        } else {
            $payment->received_by = 'admin';
        }

        $payment->admin_commission = $admin_commission;
        $payment->cancel_charges = $cancel_charges;
        $payment->save();

        $admin_wallet = Wallet::firstOrCreate(
            [ 'user_id' => $admin_id ]
        );

        if($payment->payment_status == 'paid') {
            // payment done through card/wallet
            if( $payment->payment_type != 'cash' ) {

                $order_cancel_refund = $order_amount - $cancel_charges;
                $client_wallet->increment('total_amount', $order_cancel_refund );

                if( $order_history > 0) {
                    $delivery_man_wallet = Wallet::firstOrCreate(
                        [ 'user_id' => $order->delivery_man_id ]
                    );
                    $delivery_man_commission = $payment->delivery_man_commission;
                    if( $payment->payment_type != 'cash' )
                    {
                        $admin_wallet->decrement('total_amount', $delivery_man_commission );
                        $admin_wallet_history = [
                            'user_id'           => $admin_id,
                            'type'              => 'debit',
                            'transaction_type'  => 'correction',
                            'currency'          => $currency,
                            'amount'            => $delivery_man_commission,
                            'balance'           => $admin_wallet->total_amount,
                            'order_id'          => $payment->order_id,
                            'datetime'          => date('Y-m-d H:i:s'),
                            'data' => [
                                'payment_id'    => $payment->id,
                                'order_history' => $order_history
                            ]
                        ];
                        WalletHistory::create($admin_wallet_history);

                        $delivery_man_wallet->increment('total_amount', $delivery_man_commission );

                        $delivery_man_wallet_history = [
                            'user_id'           => $order->delivery_man_id,
                            'type'              => 'credit',
                            'currency'          => $currency,
                            'transaction_type'  => 'commission',
                            'amount'            => $delivery_man_commission,
                            'balance'           => $delivery_man_wallet->total_amount,
                            'order_id'          => $order->id,
                            'datetime'          => date('Y-m-d H:i:s'),
                            'data' => [
                                'payment_id'    => $payment->id,
                                'order_history' => $order_history
                            ]
                        ];
                        WalletHistory::create($delivery_man_wallet_history);
                    }
                } else {
                    if ($payment->payment_type == 'wallet') {
                        $admin_wallet->increment('total_amount', $cancel_charges );
                        $admin_wallet_history = [
                            'user_id'           => $admin_id,
                            'type'              => 'credit',
                            'transaction_type'  => 'commission',
                            'currency'          => $currency,
                            'amount'            => $cancel_charges,
                            'balance'           => $admin_wallet->total_amount,
                            'order_id'          => $payment->order_id,
                            'datetime'          => date('Y-m-d H:i:s'),
                            'data' => [
                                'payment_id'    => $payment->id,
                                'order_history' => $order_history
                            ]
                        ];
                        WalletHistory::create($admin_wallet_history);
                    }
                }

                $client_wallet_history = [
                    'user_id'           => $order->client_id,
                    'type'              => 'credit',
                    'currency'          => $currency,
                    'transaction_type'  => 'order_cancel_refund',
                    'amount'            => $order_cancel_refund,
                    'balance'           => $client_wallet->total_amount,
                    'order_id'          => $order->id,
                    'datetime'          => date('Y-m-d H:i:s'),
                    'data' => [
                        'payment_id'        => $payment->id,
                        'cancel_charges'    => $payment->cancel_charges,
                        'order_history'     => $order_history
                    ]
                ];
                WalletHistory::create($client_wallet_history);
            }

            if( $payment->payment_type == 'cash' ) {
                /*
                $client_wallet->decrement('total_amount', $cancel_charges );

                $client_wallet_history = [
                    'user_id'           => $order->client_id,
                    'type'              => 'debit',
                    'currency'          => $currency,
                    'transaction_type'  => 'order_cancel_charge',
                    'amount'            => $cancel_charges,
                    'balance'           => $client_wallet->total_amount,
                    'order_id'          => $order->id,
                    'datetime'          => date('Y-m-d H:i:s'),
                    'data' => [
                        'payment_id'        => $payment->id,
                        'cancel_charges'    => $cancel_charges,
                        'order_history'     => $order_history
                    ]
                ];
                WalletHistory::create($client_wallet_history);
                */

                $delivery_man_wallet = Wallet::firstOrCreate(
                    [ 'user_id' => $order->delivery_man_id ]
                );
                $delivery_man_wallet->decrement('total_amount', $admin_commission );
                $delivery_man_wallet_history = [
                    'user_id'           => $order->delivery_man_id,
                    'type'              => 'debit',
                    'currency'          => $currency,
                    'transaction_type'  => 'correction',
                    'amount'            => $admin_commission,
                    'balance'           => $delivery_man_wallet->total_amount,
                    'order_id'          => $order->id,
                    'datetime'          => date('Y-m-d H:i:s'),
                    'data' => [
                        'payment_id'        => $payment->id,
                        'admin_commission'  => $admin_commission,
                        'order_history'     => $order_history
                    ]
                ];
                WalletHistory::create($delivery_man_wallet_history);

                if( $order_history > 0 ) {
                    $admin_wallet->increment('total_amount', $admin_commission );
                    $admin_wallet_history = [
                        'user_id'           => $admin_id,
                        'type'              => 'credit',
                        'transaction_type'  => 'commission',
                        'currency'          => $currency,
                        'amount'            => $admin_commission,
                        'balance'           => $admin_wallet->total_amount,
                        'order_id'          => $payment->order_id,
                        'datetime'          => date('Y-m-d H:i:s'),
                        'data' => [
                            'payment_id'    => $payment->id,
                            'order_history' => $order_history,
                            'admin_commission' => $admin_commission
                        ]
                    ];
                    WalletHistory::create($admin_wallet_history);
                    /*
                    $delivery_man_wallet = Wallet::firstOrCreate(
                        [ 'user_id' => $order->delivery_man_id ]
                    );
                    $delivery_man_commission = $payment->delivery_man_commission;
                    $delivery_man_wallet->increment('total_amount', $delivery_man_commission );

                    $delivery_man_wallet_history = [
                        'user_id'           => $order->delivery_man_id,
                        'type'              => 'credit',
                        'currency'          => $currency,
                        'transaction_type'  => 'commission',
                        'amount'            => $delivery_man_commission,
                        'balance'           => $delivery_man_wallet->total_amount,
                        'order_id'          => $order->id,
                        'datetime'          => date('Y-m-d H:i:s'),
                        'data' => [
                            'payment_id'    => $payment->id,
                            'order_history' => $order_history,
                            'delivery_man_commission' => $delivery_man_commission,
                        ]
                    ];
                    WalletHistory::create($delivery_man_wallet_history);
                    */
                }
            }
        }
        if($payment->payment_status == 'pending') {
            $client_wallet->decrement('total_amount', $cancel_charges );

            $client_wallet_history = [
                'user_id'           => $order->client_id,
                'type'              => 'debit',
                'currency'          => $currency,
                'transaction_type'  => 'order_cancel_charge',
                'amount'            => $cancel_charges,
                'balance'           => $client_wallet->total_amount,
                'order_id'          => $order->id,
                'datetime'          => date('Y-m-d H:i:s'),
                'data' => [
                    'payment_id'        => $payment->id,
                    'cancel_charges'    => $cancel_charges,
                    'order_history'     => $order_history
                ]
            ];
            WalletHistory::create($client_wallet_history);

            $admin_wallet->increment('total_amount', $admin_commission );
            $admin_wallet_history = [
                'user_id'           => $admin_id,
                'type'              => 'credit',
                'transaction_type'  => 'commission',
                'currency'          => $currency,
                'amount'            => $admin_commission,
                'balance'           => $admin_wallet->total_amount,
                'order_id'          => $payment->order_id,
                'datetime'          => date('Y-m-d H:i:s'),
                'data' => [
                    'payment_id'    => $payment->id,
                    'order_history' => $order_history,
                    'admin_commission' => $admin_commission
                ]
            ];
            WalletHistory::create($admin_wallet_history);

            if( $order->delivery_man_id != null && $order_history > 0 ) {
                $delivery_man_wallet = Wallet::firstOrCreate(
                    [ 'user_id' => $order->delivery_man_id ]
                );
                $delivery_man_commission = $payment->delivery_man_commission;
                $delivery_man_wallet->increment('total_amount', $delivery_man_commission );

                $delivery_man_wallet_history = [
                    'user_id'           => $order->delivery_man_id,
                    'type'              => 'credit',
                    'currency'          => $currency,
                    'transaction_type'  => 'commission',
                    'amount'            => $delivery_man_commission,
                    'balance'           => $delivery_man_wallet->total_amount,
                    'order_id'          => $order->id,
                    'datetime'          => date('Y-m-d H:i:s'),
                    'data' => [
                        'payment_id'    => $payment->id,
                        'order_history' => $order_history,
                        'delivery_man_commission' => $delivery_man_commission,
                    ]
                ];
                WalletHistory::create($delivery_man_wallet_history);
            }
            $payment->update(['payment_status' => 'paid']);
            $order->update(['payment_id' => $payment->id]);
        }
        // return true;
    }

    public function walletTransactionCompleted($order_id)
    {
        $order = Order::where('id', $order_id)->first();

        if( $order == null ) {
            return false;
        }

        $admin_id = User::admin()->id;
        $payment = Payment::where('order_id',$order_id)->first();

        $commission_type = $order->city->commission_type ?? 0;
        $admin_commission = $order->city->admin_commission ?? 0;

        // tip not added in the order total_amount
        $order_amount = $payment->total_amount;

        if( $commission_type == 'percentage' ) {
            $admin_commission = $admin_commission ? ( $order_amount / 100) * $admin_commission: 0;
        }

        if( $payment->payment_type == 'cash') {
            $payment->received_by = 'delivery_man';
        } elseif ($payment->payment_type == 'wallet') {
            $payment->received_by = 'wallet';
        } else {
            $payment->received_by = 'admin';
        }

        $payment->admin_commission = $admin_commission;

        $delivery_man_fee = $order_amount - $admin_commission;
        $payment->delivery_man_fee = $delivery_man_fee;
        $payment->delivery_man_commission = $delivery_man_fee + $payment->delivery_man_tip;
        $payment->save();

        $currency = appSettingData('currency_code')->currency;
        try {
            DB::beginTransaction();

            if( $payment->payment_type == 'wallet' )
            {
                $delivery_man_wallet = Wallet::firstOrCreate(
                    [ 'user_id' => $order->delivery_man_id ]
                );
                $delivery_man_wallet->total_amount += $payment->delivery_man_commission;
                $delivery_man_wallet->save();

                $delivery_man_wallet_history = [
                    'user_id'           => $order->delivery_man_id,
                    'type'              => 'credit',
                    'transaction_type'  => 'Received commission from Order#'.$payment->order_id,
                    'currency'          => $currency,
                    'amount'            => $payment->delivery_man_commission,
                    'balance'           => $delivery_man_wallet->total_amount,
                    'order_id'          => $payment->order_id,
                    'datetime'          => date('Y-m-d H:i:s'),
                    'data' => [
                        'payment_id'    => $payment->id,
                        'tip'           => $payment->delivery_man_tip
                    ]
                ];
                WalletHistory::create($delivery_man_wallet_history);

                $admin_wallet = Wallet::firstOrCreate(
                    [ 'user_id' => $admin_id ]
                );

                $admin_wallet->total_amount -= $payment->delivery_man_commission;
                $admin_wallet->save();

                $admin_wallet_history = [
                    'user_id'           => $admin_id,
                    'type'              => 'debit',
                    'transaction_type'  => 'Deducted delivery man commission from Order#'.$payment->order_id,
                    'currency'          => $currency,
                    'amount'            => $payment->delivery_man_commission,
                    'balance'           => $admin_wallet->total_amount,
                    'order_id'          => $payment->order_id,
                    'datetime'          => date('Y-m-d H:i:s'),
                    'data' => [
                        'payment_id'    => $payment->id,
                    ]
                ];
                WalletHistory::create($admin_wallet_history);

            } elseif ($payment->payment_type == 'cash') {
                $delivery_man_wallet = Wallet::firstOrCreate(
                    [ 'user_id' => $order->delivery_man_id ]
                );
                $delivery_man_wallet->total_amount -= $admin_commission;
                $delivery_man_wallet->save();

                $delivery_man_wallet_history = [
                    'user_id'           => $order->delivery_man_id,
                    'type'              => 'debit',
                    'transaction_type'  => 'Deducted admin commission from order#'.$payment->order_id,
                    'currency'          => $currency,
                    'amount'            => $admin_commission,
                    'balance'           => $delivery_man_wallet->total_amount,
                    'order_id'          => $payment->order_id,
                    'datetime'          => date('Y-m-d H:i:s'),
                ];
                WalletHistory::create($delivery_man_wallet_history);

                $admin_wallet = Wallet::firstOrCreate(
                    [ 'user_id' => $admin_id ]
                );
                $admin_wallet->total_amount = $admin_wallet->total_amount + $admin_commission;
                $admin_wallet->save();

                $admin_wallet_history = [
                    'user_id'           => $admin_id,
                    'type'              => 'credit',
                    'transaction_type'  => 'Received commission from Order#'.$payment->order_id,
                    'currency'          => $currency,
                    'amount'            => $admin_commission,
                    'balance'           => $admin_wallet->total_amount,
                    'order_id'          => $payment->order_id,
                    'datetime'          => date('Y-m-d H:i:s'),
                    'data' => [
                        'payment_id'    => $payment->id
                    ]
                ];
                WalletHistory::create($admin_wallet_history);

            } else {

                $admin_wallet = Wallet::firstOrCreate(
                    [ 'user_id' => $admin_id ]
                );
                $admin_wallet->total_amount -= $payment->delivery_man_commission;
                $admin_wallet->save();

                $admin_wallet_history = [
                    'user_id'           => $admin_id,
                    'type'              => 'debit',
                    'transaction_type'  => 'Deducted delivery man commission from Order#'.$payment->order_id,
                    'currency'          => $currency,
                    'amount'            => $payment->delivery_man_commission,
                    'balance'           => $admin_wallet->total_amount,
                    'order_id'          => $payment->order_id,
                    'datetime'          => date('Y-m-d H:i:s'),
                    'data' => [
                        'payment_id'    => $payment->id,
                    ]
                ];
                WalletHistory::create($admin_wallet_history);

                $delivery_man_wallet = Wallet::firstOrCreate(
                    [ 'user_id' => $order->delivery_man_id ]
                );
                $delivery_man_wallet->total_amount += $payment->delivery_man_commission;
                $delivery_man_wallet->save();

                $delivery_man_wallet_history = [
                    'user_id'           => $order->delivery_man_id,
                    'type'              => 'credit',
                    'transaction_type'  => 'Received commission from Order#'.$payment->order_id,
                    'currency'          => $currency,
                    'amount'            => $payment->delivery_man_commission,
                    'balance'           => $delivery_man_wallet->total_amount,
                    'order_id'          => $payment->order_id,
                    'datetime'          => date('Y-m-d H:i:s'),
                    'data' => [
                        'payment_id'    => $payment->id,
                        'tip'           => $payment->delivery_man_tip,
                    ]
                ];
                WalletHistory::create($delivery_man_wallet_history);
            }
            DB::commit();
        } catch(\Exception $e) {
            // \Log::info($e);
            DB::rollBack();
            return json_custom_response($e);
        }

        return true;
    }
}
