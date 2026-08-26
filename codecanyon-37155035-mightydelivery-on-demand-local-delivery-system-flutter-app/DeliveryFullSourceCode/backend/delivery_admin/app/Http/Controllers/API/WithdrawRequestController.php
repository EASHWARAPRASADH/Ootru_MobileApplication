<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletHistory;
use App\Models\WithdrawRequest;
use App\Http\Resources\WithdrawRequestResource;
use App\Http\Requests\WithdrawRequestRequest;
use App\Notifications\CommonNotification;
use Illuminate\Support\Facades\DB;

class WithdrawRequestController extends Controller
{
    public function getList(Request $request)
    {
        $withdraw = WithdrawRequest::myWithdrawRequest();

        $withdraw->when(request('user_id'), function ($q) {
            return $q->where('user_id', request('user_id'));
        });

        $withdraw->when(request('status'), function ($q) {
            return $q->where('status', request('status'));
        });

        $per_page = config('constant.PER_PAGE_LIMIT');
        if( $request->has('per_page') && !empty($request->per_page)){

            if(is_numeric($request->per_page))
            {
                $per_page = $request->per_page;
            }

            if($request->per_page == -1 ){
                $per_page = $withdraw->count();
            }
        }

        $withdraw = $withdraw->orderBy('id','desc')->paginate($per_page);
        $items = WithdrawRequestResource::collection($withdraw);

        $wallet_data = Wallet::where('user_id', auth()->user()->id)->first();
        $response = [
            'pagination' => json_pagination_response($items),
            'data' => $items,
            'wallet_data' => $wallet_data
        ];

        return json_custom_response($response);
    }

    public function saveWithdrawrequest(Request $request)
    {
        $data = $request->all();
        $data['user_id'] = auth()->user()->id ?? $request->user_id;

        $withdrawrequest_exist = WithdrawRequest::where('user_id', $data['user_id'])->where('status', 'requested')->exists();
        if($withdrawrequest_exist) {
            $message = __('message.already_withdrawrequest');
            return json_message_response( $message, 400 );
        }
        $withdrawrequest = WithdrawRequest::create($data);

        $message = __('message.save_form',['form' => __('message.withdrawrequest')]);

        return json_message_response( $message );
    }

    public function approvedWithdrawRequest(Request $request)
    {
        $withdrawrequest = WithdrawRequest::where('id', $request->id)->first();

        if( $withdrawrequest == null ) {
            $message = __('message.not_found_entry',['name' => __('message.withdrawrequest')]);
            if($request->is('api/*')) {
                return json_message_response($message);
            }
            return redirect()->back()->withErrors($message);
        }

        if( $withdrawrequest->status == 'approved' ) {
            $message = __('message.already_approved_withdrawrequest',['name' => __('message.withdrawrequest')]);
            if($request->is('api/*')) {
                return json_message_response($message);
            }
            return redirect()->back()->withErrors($message);
        }

        $wallet = Wallet::where('user_id', $withdrawrequest->user_id)->first();

        if (is_null($wallet) || $wallet->total_amount === null) {
            $message = __('message.total_amount_null');

            if ($request->is('api/*')) {
                return json_message_response($message);
            } else {
                return redirect()->back()->withErrors($message);
            }
        } elseif ($wallet->total_amount == 0) {
            $message = __('message.total_amount_zero');

            if ($request->is('api/*')) {
                return json_message_response($message);
            } else {
                return redirect()->back()->withErrors($message);
            }
        }

        if( $wallet->total_amount < $withdrawrequest->amount ) {
            $message = __('message.wallet_balance_insufficient');
            if($request->is('api/*')) {
                return json_message_response($message);
            }
            return redirect()->back()->withErrors($message);
        }

        $data = [
            'status'    => 'approved',
        ];
        $user = User::find($withdrawrequest->user_id);
        $wallet = Wallet::where('user_id', $withdrawrequest->user_id)->first();
        if( $wallet != null ) {
            try
            {
                DB::beginTransaction();
                $withdrawrequest->update($data);

                $wallet->user_id           = $withdrawrequest->user_id;
                $wallet->total_amount      = $wallet->total_amount - $withdrawrequest->amount;
                $wallet->total_withdrawn   = $wallet->total_withdrawn + $withdrawrequest->amount;
                $wallet->currency          = $withdrawrequest->currency;

                $wallet->save();

                $wallet_history_data = [
                    'user_id'           => $withdrawrequest->user_id,
                    'type'              => 'debit',
                    'transaction_type'  => 'withdraw',
                    'amount'            => $withdrawrequest->amount,
                    'balance'           => $wallet->total_amount,
                    'currency'          => $withdrawrequest->currency,
                    'datetime'          => date('Y-m-d H:i:s'),
                ];

                WalletHistory::create($wallet_history_data);

                $message = __('message.withdrawrequest_approved');
                $notification_data = [
                    'id'   => $withdrawrequest->id,
                    'type' => 'withdrawrequest_approved',
                    'subject' => __('message.withdrawrequest'),
                    'message' => $message,
                ];
                $user->notify(new CommonNotification($notification_data['type'], $notification_data));
                DB::commit();

            } catch(\Exception $e) {
                DB::rollBack();
                return redirect()->back();
            }
        }

         if($request->is('api/*')) {
            return json_message_response($message);
		}
        return redirect()->back()->withSuccess($message);
    }

    public function declineWithdrawRequest(Request $request)
    {
        $withdrawrequest = WithdrawRequest::where('id', $request->id)->first();

        if( $withdrawrequest == null ) {
            $message = __('message.not_found_entry',['name' => __('message.withdrawrequest')]);
            if($request->is('api/*')) {
                return json_message_response($message);
            }
            return redirect()->back()->withErrors($message);
        }

        $data = [
            'status'    => 'decline',
        ];
        $user = User::find($withdrawrequest->user_id);

        $withdrawrequest->update($data);
        $notification_data = [
            'id'        => $withdrawrequest->id,
            'type'      => 'withdrawrequest_decline',
            'subject'   => __('message.withdrawrequest'),
            'message'   => __('message.withdrawrequest_decline'),
        ];
        $user->notify(new CommonNotification($notification_data['type'], $notification_data));
        $message = __('message.withdrawrequest_declined');
        if($request->is('api/*')) {
            return json_message_response($message);
		}
        return redirect()->back()->withSuccess($message);
    }
}
