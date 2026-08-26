<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\City;
use App\Http\Resources\CityResource;

class CityController extends Controller
{
    public function getList(Request $request)
    {
        $city = City::query()->whereStatus(1);

        $city->when(request('country_id'), function ($q) {
            return $q->where('country_id', request('country_id'));
        });

        $city->when(request('search'), function ($q) {
            return $q->where('name', 'LIKE', '%' . request('search') . '%')
                    ->orWhereHas('country', function ($query) {
                        $query->where('name', 'LIKE', '%' . request('search') . '%');
                    });
        });

        if( $request->has('status') && isset($request->status) )
        {
            $city = $city->where('status',request('status'));
        }

        if( $request->has('is_deleted') && isset($request->is_deleted) && $request->is_deleted){
            $city = $city->withTrashed();
        }

        $per_page = config('constant.PER_PAGE_LIMIT');
        if( $request->has('per_page') && !empty($request->per_page)){
            if(is_numeric($request->per_page))
            {
                $per_page = $request->per_page;
            }
            if($request->per_page == -1 ){
                $per_page = $city->count();
            }
        }

        $city = $city->orderBy('name','asc')->paginate($per_page);
        $items = CityResource::collection($city);

        $response = [
            'pagination' => json_pagination_response($items),
            'data' => $items,
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
