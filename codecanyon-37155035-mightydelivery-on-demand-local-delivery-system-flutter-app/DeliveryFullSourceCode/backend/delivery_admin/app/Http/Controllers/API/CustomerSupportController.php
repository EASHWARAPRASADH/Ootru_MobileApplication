<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\City;
use App\Http\Resources\CityResource;
use App\Http\Resources\CustomerSupportResource;
use App\Models\CustomerSupport;

class CustomerSupportController extends Controller
{
    public function getList(Request $request)
    {
        $user = auth()->user();
        if($user->hasRole('admin')){
            $support = CustomerSupport::query();
        }else{
            $support = CustomerSupport::myCustomersupport();
        }
        // $support = CustomerSupport::myCustomersupport();

        $support->when(request('support_id'), function ($q) {
            return $q->where('id', request('support_id'));
        });
        if( $request->has('status') && isset($request->status) ) {
            $support = $support->where('status',request('status'));
        }
        
        $per_page = config('constant.PER_PAGE_LIMIT');
        if( $request->has('per_page') && !empty($request->per_page)){
            if(is_numeric($request->per_page))
            {
                $per_page = $request->per_page;
            }
            if($request->per_page == -1 ){
                $per_page = $support->count();
            }
        }

        $support = $support->orderBy('id', 'desc')->paginate($per_page);
        $items = CustomerSupportResource::collection($support);

        $current_user = auth()->user();
        if(count($current_user->unreadNotifications) > 0 ) {
            $current_user->unreadNotifications->where('data.sender_id',request('sender_id'))->markAsRead();
        }
        $response = [
            'pagination' => json_pagination_response($items),
            'customersupport' => $items,
            // 'supportchat_histriry' =>SupportchatHistoryResource::collection($support),
        ];
        
        return json_custom_response($response);
    }



    public function getDetail(Request $request)
    {
        $id = $request->id;

        $city = City::where('id',$id)->first();
        if(empty($city))
        {
            $message = __('message.not_found_entry',['name' =>__('message.city')]);
            return json_message_response($message,400);
        }

        $city_detail = new CityResource($city);
        $response = [
            'data' => $city_detail
        ];
        return json_custom_response($response);
    }

    public function multipleDeleteRecords(Request $request)
    {
        $multi_ids = $request->ids;
        $user_type = $request->user_type != null ? $request->user_type : 'client';
        $message = __('message.msg_fail_to_delete', ['item' => __('message.'.$user_type)]);

        foreach ($multi_ids as $id) {
            $user = City::withTrashed()->where('id',$id)->first();
            if ($user) {
                if( $user->deleted_at != null ) {
                    $user->forceDelete();
                } else {
                    $user->delete();
                }
                $message = __('message.msg_deleted',['name' => __('message.'.$user->user_type) ]);
            }
        }

        return json_custom_response(['message'=> $message , 'status' => true]);
    }
}