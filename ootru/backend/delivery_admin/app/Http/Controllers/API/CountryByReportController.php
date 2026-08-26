<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Http\Resources\CountryByReportResource;
use App\Models\Country;

class CountryByReportController extends Controller
{
    public function getList(Request $request)
    {
        if (!$request->has('country_id')) {
            $message = __('message.please_prvide_country_id_to_retrieve_the_report');
            return json_message_response($message);
        }
        $report = Order::query()->where('status', 'completed');

        $countryid = Country::pluck('id')->toArray();
        $report->whereIn('country_id', $countryid);
        
        $report->when(request('country_id'), function ($q) {
            return $q->where('country_id', request('country_id'));
        });

        if ($request->has('from_date')) {
            $report->whereDate('created_at', '>=', $request->input('from_date'));
        }
        if ($request->has('to_date')) {
            $report->whereDate('created_at', '<=', $request->input('to_date'));
        }


        $per_page = config('constant.PER_PAGE_LIMIT');
        if( $request->has('per_page') && !empty($request->per_page)){
            if(is_numeric($request->per_page))
            {
                $per_page = $request->per_page;
            }
            if($request->per_page == -1 ){
                $per_page = $report->count();
            }
        }

        $totalAdminCommission = $report->with('payment')->get()->sum(function ($order) {
            return optional($order->payment)->admin_commission ?? 0;
        });
        $totalDeliveryManCommission = $report->with('payment')->get()->sum(function ($order) {
            return optional($order->payment)->delivery_man_commission ?? 0;
        });
        $totalamount = $report->with('payment')->get()->sum(function ($order) {
            return optional($order->payment)->total_amount ?? 0;
        });

        $report = $report->orderBy('id','asc')->paginate($per_page);
        $items = CountryByReportResource::collection($report);

        $response = [
            'pagination' => json_pagination_response($items),
            'data' => $items,
            'total_admin_commission' => $totalAdminCommission,
            'total_delivery_man_commission' => $totalDeliveryManCommission,
            'total_amount' => $totalamount,
        ];

        return json_custom_response($response);
    }
}