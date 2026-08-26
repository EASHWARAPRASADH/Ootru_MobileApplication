<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Http\Resources\VehicleResource;

class VehicleController extends Controller
{
    public function getList(Request $request)
    {
        $vehicle = Vehicle::query()->whereStatus(1);
        
        if( $request->has('status') && isset($request->status) )
        {
            $vehicle = $vehicle->where('status',request('status'));
        }

        $vehicle->when(request('search'), function ($q) {
            return $q->where('title', 'LIKE', '%' . request('search') . '%');
        });

        if( $request->has('is_deleted') && isset($request->is_deleted) && $request->is_deleted){
            $vehicle = $vehicle->withTrashed();
        }

        $vehicle->when(request('city_id'), function ($q) {
            $city_id = request('city_id');
            return $q->whereJsonContains('city_ids', $city_id)->orWhere('type','all');
        });

        $vehicle->when(request('title'), function ($q) {
            return $q->where('title', 'LIKE', '%' . request('title') . '%');
        });

        $per_page = config('constant.PER_PAGE_LIMIT');
        if( $request->has('per_page') && !empty($request->per_page)){
            if(is_numeric($request->per_page))
            {
                $per_page = $request->per_page;
            }
            if($request->per_page == -1 ){
                $per_page = $vehicle->count();
            }
        }

        $vehicle = $vehicle->orderBy('id','desc')->paginate($per_page);
        $items = VehicleResource::collection($vehicle);

        $response = [
            'pagination' => json_pagination_response($items),
            'data' => $items,
        ];

        return json_custom_response($response);
    }

    public function multipleDeleteRecords(Request $request)
    {
        $multi_ids = $request->ids;
        $user_type = $request->user_type != null ? $request->user_type : 'client';
        $message = __('message.msg_fail_to_delete', ['item' => __('message.'.$user_type)]);

        foreach ($multi_ids as $id) {
            $user = Vehicle::withTrashed()->where('id',$id)->first();
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
