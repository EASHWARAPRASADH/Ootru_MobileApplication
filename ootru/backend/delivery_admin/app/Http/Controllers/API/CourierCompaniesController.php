<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CourierCompanies;
use App\Http\Resources\CourierCompaniesResource;

class CourierCompaniesController extends Controller
{
    public function getList(Request $request)
    {
        $couriercompanies = CourierCompanies::query()->where('status',1);

        $per_page = config('constant.PER_PAGE_LIMIT');
        if( $request->has('per_page') && !empty($request->per_page)){
            if(is_numeric($request->per_page))
            {
                $per_page = $request->per_page;
            }
            if($request->per_page == -1 ){
                $per_page = $couriercompanies->count();
            }
        }

        $couriercompanies = $couriercompanies->orderBy('name', 'asc')->paginate($per_page);

        $items = CourierCompaniesResource::collection($couriercompanies);
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

        $couriercompanies = CourierCompanies::where('id',$id)->first();
        if(empty($couriercompanies))
        {
            $message = __('message.not_found_entry',['name' =>__('message.country')]);
            return json_message_response($message,400);
        }

        $couriercompanies_detail = new CourierCompaniesResource($couriercompanies);
        $response = [
            'data' => $couriercompanies_detail
        ];
        return json_custom_response($response);
    }

}
