<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExtraCharge;
use App\Http\Resources\ExtraChargesResource;

class ExtraChargeController extends Controller
{
    public function getList(Request $request)
    {
        $extracharge = ExtraCharge::query()->whereStatus(1);

        $per_page = config('constant.PER_PAGE_LIMIT');
        if( $request->has('per_page') && !empty($request->per_page)){
            if(is_numeric($request->per_page))
            {
                $per_page = $request->per_page;
            }
            if($request->per_page == -1 ){
                $per_page = $extracharge->count();
            }
        }

        $extracharge = $extracharge->orderBy('title', 'asc')->paginate($per_page);

        $items = ExtraChargesResource::collection($extracharge);
        $status = 200;

        $response = [
            'status'        => $status,
            'pagination'    => json_pagination_response($items),
            'data'          => $items,
        ];

        return json_custom_response($response);
    }
}
