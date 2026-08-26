<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\DeliverymanVehicleHistoryResource;
use App\Models\DeliverymanVehicleHistory;

class DeliverymanVehicleHistoryController extends Controller
{
    public function getList(Request $request)
    {
        $user = auth()->user();
        if($user){
            $DeliverymanVehicle = DeliverymanVehicleHistory::where('delivery_man_id', $user->id);;

            $per_page = config('constant.PER_PAGE_LIMIT');
            if( $request->has('per_page') && !empty($request->per_page)){
                if(is_numeric($request->per_page))
                {
                    $per_page = $request->per_page;
                }
                if($request->per_page == -1 ){
                    $per_page = $DeliverymanVehicle->count();
                }
            }
        }
        $DeliverymanVehicle = $DeliverymanVehicle->orderBy('id','desc')->paginate($per_page);
        $items = DeliverymanVehicleHistoryResource::collection($DeliverymanVehicle);

        $response = [
            'pagination' => json_pagination_response($items),
            'data' => $items,
        ];

        return json_custom_response($response);
    }

}
