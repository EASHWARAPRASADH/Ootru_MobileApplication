<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StaticData;
use App\Http\Resources\StaticDataResource;

class StaticDataController extends Controller
{
    public function getList(Request $request)
    {
        $parceltype = StaticData::query();

        $per_page = config('constant.PER_PAGE_LIMIT');
        if( $request->has('per_page') && !empty($request->per_page)){
            if(is_numeric($request->per_page))
            {
                $per_page = $request->per_page;
            }
            if($request->per_page == -1 ){
                $per_page = $parceltype->count();
            }
        }

        $parceltype = $parceltype->orderBy('label', 'asc')->paginate($per_page);

        $items = StaticDataResource::collection($parceltype);
        $status = 200;

        $response = [
            'status'        => $status,
            'pagination'    => json_pagination_response($items),
            'data'          => $items,
        ];

        return json_custom_response($response);
    }
}
