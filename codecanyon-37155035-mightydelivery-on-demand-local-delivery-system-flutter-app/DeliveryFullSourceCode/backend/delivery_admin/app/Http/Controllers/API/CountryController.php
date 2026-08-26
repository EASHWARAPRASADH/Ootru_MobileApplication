<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Http\Resources\CountryResource;

class CountryController extends Controller
{
    public function getList(Request $request)
    {
        $country = Country::query()->whereStatus(1);

        $per_page = config('constant.PER_PAGE_LIMIT');
        if( $request->has('per_page') && !empty($request->per_page)){
            if(is_numeric($request->per_page))
            {
                $per_page = $request->per_page;
            }
            if($request->per_page == -1 ){
                $per_page = $country->count();
            }
        }

        $country = $country->orderBy('name', 'asc')->paginate($per_page);

        $items = CountryResource::collection($country);
        $status = 200;

        $response = [
            'status'        => $status,
            'pagination'    => json_pagination_response($items),
            'data'          => $items,
        ];

        return json_custom_response($response);
    }


    public function getDetail(Request $request)
    {
        $id = $request->id;

        $country = Country::where('id',$id)->first();
        if(empty($country))
        {
            $message = __('message.not_found_entry',['name' =>__('message.country')]);
            return json_message_response($message,400);
        }

        $country_detail = new CountryResource($country);
        $response = [
            'data' => $country_detail
        ];
        return json_custom_response($response);
    }

    public function multipleDeleteRecords(Request $request)
    {
        $multi_ids = $request->ids;
        $user_type = $request->user_type != null ? $request->user_type : 'client';
        $message = __('message.msg_fail_to_delete', ['item' => __('message.'.$user_type)]);

        foreach ($multi_ids as $id) {
            $user = Country::withTrashed()->where('id',$id)->first();
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
