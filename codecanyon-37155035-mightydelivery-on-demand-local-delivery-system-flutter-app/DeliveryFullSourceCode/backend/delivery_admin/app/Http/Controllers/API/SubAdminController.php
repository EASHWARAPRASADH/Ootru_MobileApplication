<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Models\User;

class SubAdminController extends Controller
{
    public function getList(Request $request)
    {
        $subadmin = User::whereNotIn('user_type', ['admin','client','delivery_man']);

        $per_page = config('constant.PER_PAGE_LIMIT');
        if( $request->has('per_page') && !empty($request->per_page)){
            if(is_numeric($request->per_page))
            {
                $per_page = $request->per_page;
            }
            if($request->per_page == -1 ){
                $per_page = $subadmin->count();
            }
        }

        $subadmin = $subadmin->orderBy('id', 'asc')->paginate($per_page);

        $items = UserResource::collection($subadmin);
        $status = 200;

        $response = [
            'status'        => $status,
            'pagination'    => json_pagination_response($items),
            'data'          => $items,
        ];

        return json_custom_response($response);
    }
}
