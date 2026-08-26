<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Coupon;
use App\Http\Resources\CouponResource;
use App\Models\Order;
use Carbon\Carbon;

class CouponController extends Controller
{
    public function getList(Request $request)
    {
        $user = Auth()->user();
        $userCityId = $user->city_id; 
    
        $currentDate = Carbon::now()->format('Y-m-d');
        $usedCouponCodes = Order::where('client_id', $user->id)
        ->whereNotNull('coupon_code')   
        ->pluck('coupon_code')
        ->toArray();
    
        $coupon = Coupon::query()
            ->where('status', 1)
            ->where(function($query) use ($userCityId) {
                $query->whereJsonContains('city_id', strval($userCityId))
                      ->orWhere('city_type', 'all');
            })
            ->whereDate('start_date', '<=', $currentDate) 
            ->whereDate('end_date', '>=', $currentDate)
            ->whereNotIn('coupon_code', $usedCouponCodes);

        $per_page = config('constant.PER_PAGE_LIMIT');
        if( $request->has('per_page') && !empty($request->per_page)){
            if(is_numeric($request->per_page))
            {
                $per_page = $request->per_page;
            }
            if($request->per_page == -1 ){
                $per_page = $coupon->count();
            }
        }

        $coupon = $coupon->orderBy('id', 'desc')->paginate($per_page);

        $items = CouponResource::collection($coupon);
        $status = 200;

        $response = [
            'status'        => $status,
            'pagination'    => json_pagination_response($items),
            'data'          => $items,
        ];

        return json_custom_response($response);
    }
}
