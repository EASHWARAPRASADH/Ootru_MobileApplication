<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Http\Resources\AdminEarningReportResource;
use App\Models\User;

class AdminEarningReportController extends Controller
{
    public function getList(Request $request)
    {
        $userIds = User::where('user_type', ['client', 'delivery_man'])->pluck('id')->toArray();

        $report = Order::where(function($query) use ($userIds) {
            $query->whereIn('client_id', $userIds)
                  ->orWhereIn('delivery_man_id', $userIds);
            })->where('status', 'completed');

        if ($request->has('from_date')) {
            $report->whereDate('created_at', '>=', $request->input('from_date'));
        }
        if ($request->has('to_date')) {
            $report->whereDate('created_at', '<=', $request->input('to_date'));
        }


        $per_page = config('constant.PER_PAGE_LIMIT');
        if ($request->has('per_page') && !empty($request->per_page)) {
            if (is_numeric($request->per_page)) {
                $per_page = $request->per_page;
            }
            if ($request->per_page == -1) {
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

        $report = $report->orderBy('id', 'asc')->paginate($per_page);
        $items = AdminEarningReportResource::collection($report);

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