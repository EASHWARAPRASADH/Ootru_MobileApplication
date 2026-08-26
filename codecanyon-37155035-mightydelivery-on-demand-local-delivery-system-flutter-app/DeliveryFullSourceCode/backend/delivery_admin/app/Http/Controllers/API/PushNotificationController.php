<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentGateway;
use App\Http\Resources\PushNotificationResource;
use App\Models\PushNotification;

class PushNotificationController extends Controller
{

    public function getList(Request $request)
    {
        $pushnotification = PushNotification::query();

        $per_page = config('constant.PER_PAGE_LIMIT');
        if ($request->has('per_page') && !empty($request->per_page)) {
            if (is_numeric($request->per_page)) {
                $per_page = $request->per_page;
            }
            if ($request->per_page == -1) {
                $per_page = $pushnotification->count();
            }
        }

        $order = $pushnotification->orderBy('title', 'desc')->paginate($per_page);
        $items = PushNotificationResource::collection($order);
        $response = [
            'pagination' => json_pagination_response($items),
            'data' => $items,
        ];

        return json_custom_response($response);
    }
}
