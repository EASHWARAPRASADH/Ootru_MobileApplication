<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CourierCompaniesResource;
use App\Http\Resources\OrderBidResource;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Http\Resources\OrderResource;
use App\Http\Resources\OrderDetailResource;
use App\Http\Resources\OrderPrintResource;
use App\Models\Payment;
use App\Http\Resources\PaymentResource;
use App\Http\Resources\ProfofpicturesResource;
use App\Http\Resources\RescheduleResource;
use App\Http\Resources\UserResource;
use App\Models\City;
use App\Models\CourierCompanies;
use App\Models\ExtraCharge;
use App\Models\OrderBid;
use App\Models\Profofpictures;
use App\Models\Reschedule;
use App\Models\Wallet;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function getList(Request $request)
    {
        $order = Order::myOrder();

        if ($request->has('status') && isset($request->status)) {
            if (request('status') == 'trashed') {
                $order = $order->withTrashed();
            } else {
                $order = $order->where('status', request('status'));
            }
        };

        $order->when(request('client_id'), function ($q) {
            return $q->where('client_id', request('client_id'));
        });

        $order->when(request('delivery_man_id'), function ($query) {
            return $query->whereHas('delivery_man', function ($q) {
                $q->where('delivery_man_id', request('delivery_man_id'));
            });
        });

        $order->when(request('country_id'), function ($q) {
            return $q->where('country_id', request('country_id'));
        });

        $order->when(request('city_id'), function ($q) {
            return $q->where('city_id', request('city_id'));
        });

        $order->when(request('exclude_status'), function ($q) {
            $statuses = explode(',', request('exclude_status'));
            return $q->whereNotIn('status', $statuses);
        });

        $order->when(request('status'), function ($q) {
            $statuses = explode(',', request('status'));
            return $q->whereIn('status', $statuses);
        });

        if (request('status') === 'courier_assigned') {
            $authUser = auth()->user();
            $distanceLimit = appSettingcurrency('distance', 5); 
        
            $order = Order::where(function ($query) use ($authUser, $distanceLimit) {
                $query->where('status', 'create')
                      ->where('city_id', $authUser->city_id)
                      ->whereRaw("
                          6371 * acos(
                              cos(radians(?)) * cos(radians(JSON_UNQUOTE(JSON_EXTRACT(pickup_point, '$.latitude'))))
                              * cos(radians(JSON_UNQUOTE(JSON_EXTRACT(pickup_point, '$.longitude'))) - radians(?))
                              + sin(radians(?)) * sin(radians(JSON_UNQUOTE(JSON_EXTRACT(pickup_point, '$.latitude'))))
                          ) <= ?
                      ", [
                          $authUser->latitude,
                          $authUser->longitude,
                          $authUser->latitude,
                          $distanceLimit
                      ]);
            })
            ->orWhere(function ($query) use ($authUser) {
                $query->where('status', 'courier_assigned')
                      ->where('delivery_man_id', $authUser->id);
            });
        }

        if($request->order_type){
            switch ($request->order_type) {
                case 'pending':
                    $order = $order->where('status','create');
                    break;

                case 'schedule':
                    $tomorrow = Carbon::tomorrow();
                    $order = $order->where(function ($order) use ($tomorrow) {
                        $order->whereDate('pickup_datetime', $tomorrow)
                              ->orWhereDate('delivery_datetime', $tomorrow);
                    });
                    break;

                case 'draft':
                    $query = $order->where('status','draft');
                    break;

                case 'today':
                    $order = $order->whereDate('created_at', Carbon::today());
                    break;

                case 'inprogress':
                    $order = $order->whereIn('status', ['draft', 'courier_departed', 'courier_picked_up', 'courier_assigned','courier_arrived','active']);
                    break;

                case 'cancelled':
                    $order = $order->where('status', 'cancelled');
                    break;

                case 'completed':
                    $order = $order->where('status', 'completed');
                    break;

                case 'shipped_order':
                    $order = $order->where(function ($query) {
                        $query->whereNotNull('is_shipped')->where('is_shipped', '!=', 0);
                    });
                    break;

                default:
                    break;
            }
        }
        $order->when(request('today_date'), function ($q) {
            return $q->whereDate('date', request('today_date'));
        });

        if (request('from_date') != null && request('to_date') != null) {
            $order = $order->whereBetween('date', [request('from_date'), request('to_date')]);
        }
        $per_page = config('constant.PER_PAGE_LIMIT');
        if ($request->has('per_page') && !empty($request->per_page)) {
            if (is_numeric($request->per_page)) {
                $per_page = $request->per_page;
            }
            if ($request->per_page == -1) {
                $per_page = $order->count();
            }
        }

        $order = $order->orderBy('date', 'desc')->paginate($per_page);
        $items = OrderResource::collection($order);

        $wallet_data = Wallet::where('user_id', auth()->id())->first();
        $response = [
            'pagination' => json_pagination_response($items),
            'data' => $items,
            'wallet_data' => $wallet_data ?? null,
        ];

        return json_custom_response($response);
    }

    public function getDetail(Request $request)
    {
        $id = $request->id;
        $order = Order::where('id', $id)->withTrashed()->first();

        if ($order == null) {
            return json_message_response(__('message.not_found_entry', ['name' => __('message.order')]), 400);
        }
        $order_detail = new OrderDetailResource($order);

        $order_history = optional($order)->orderHistory;

        $payment = Payment::where('order_id', $id)->first();
        if ($payment != null) {
            $payment = new PaymentResource($payment);
        }
        $current_user = auth()->user();
        if (count($current_user->unreadNotifications) > 0) {
            $current_user->unreadNotifications->where('data.id', $id)->markAsRead();
        }

        if ($order->client_id != null) {
            $client_detail =  User::where('status', 1)->where('id', $order->client_id)->first();
            $client_detail = new UserResource($client_detail);
        }
        if ($order->delivery_man_id != null) {
            $delivery_man_detail = User::where('status', 1)->where('id', $order->delivery_man_id)->first();
            $delivery_man_detail = new UserResource($delivery_man_detail);
        }
        $courier_company_array = null;
        if ($order && $order->couriercompany_id != null) {
            $courier_company = CourierCompanies::where('id', $order->couriercompany_id)->first();
            if ($courier_company) {
                $courier_companies_detail = new CourierCompaniesResource($courier_company);
            }

            $courier_company_array = $courier_companies_detail ? $courier_companies_detail->toArray(request()) : null;
            if ($courier_company_array && isset($courier_company_array['link'])) {
                $link_parts = explode('=', $courier_company_array['link']);
                $extracted_value = isset($link_parts[1]) ? trim($link_parts[1]) : null;
                $courier_company_array['tracking_id'] = $extracted_value;
            }
        }
        $profofpictures_detail = null;
        $profofpictures = Profofpictures::where('order_id',$order->id)->first();
        if ($profofpictures) {
            $profofpictures_detail = new ProfofpicturesResource($profofpictures);
        }

        if ($order->is_reschedule != null) {
            $reschedule = Reschedule::where('id', $order->is_reschedule)->first();
            if ($reschedule) {
                $reschedule_detail = new RescheduleResource($reschedule);
            }
        }
        $response = [
            'data' => $order_detail,
            'payment' => $payment ?? null,
            'order_history' => $order_history,
            'client_detail' => $client_detail ?? null,
            'delivery_man_detail' => $delivery_man_detail ?? null,
            'courier_company_detail' => $courier_company_array,
            'profofpictures_detail' => $profofpictures_detail ?? null,
            'reschedule_detail' => $reschedule_detail ?? null,
            ];

        return json_custom_response($response);
    }
    public function multipleDeleteRecords(Request $request)
    {
        $multi_ids = $request->ids;
        $message = __('message.msg_fail_to_delete', ['item' => __('message.order')]);

        foreach ($multi_ids as $id) {
            $order = Order::withTrashed()->where('id', $id)->first();
            if ($order) {
                if ($order->deleted_at != null) {
                    $order->forceDelete();
                } else {
                    $order->delete();
                }
                $message = __('message.msg_deleted', ['name' => __('message.order')]);
            }
        }

        return json_custom_response(['message' => $message, 'status' => true]);
    }
    public function getOrderTrackingDetail(Request $request)
    {
        $id = $request->order_id;
        $order = Order::where('id', $id)->withTrashed()->first();

        if ($order == null) {
            return json_message_response(__('message.not_found_entry', ['name' => __('message.order')]), 400);
        }

        $order_history = optional($order)->orderHistory;

        $response = [
            'order_history' => $order_history,
        ];

        return json_custom_response($response);
    }
    public function orderLocationList(Request $request)
    {
        $orderlocation = Order::myOrder()->whereIn('status', ['courier_departed', 'courier_picked_up', 'courier_assigned','courier_arrived','active']);

        $per_page = config('constant.PER_PAGE_LIMIT');
        if ($request->has('per_page') && !empty($request->per_page)) {
            if (is_numeric($request->per_page)) {
                $per_page = $request->per_page;
            }
            if ($request->per_page == -1) {
                $per_page = $orderlocation->count();
            }
        }

        $order = $orderlocation->orderBy('date', 'desc')->paginate($per_page);
        $items = OrderResource::collection($order);
        $response = [
            'pagination' => json_pagination_response($items),
            'data' => $items,
        ];

        return json_custom_response($response);

    }

    public function calculateTotal(Request $request)
    {
        $city = City::find($request->city_id);
        $min_weight = 0;
        $min_distance = 0;
        $extra_charge = [];
        $vehicle_details = 0;
        $insurance = 0;
        $total_fixed = 0;
        $total_percentage = 0;
        $weight_difference = 0;
        $distance_difference = 0;
        $totalAmount = 0;
        $totalAmountAll = 0;

        if ($city) {
            $vehicle = Vehicle::find($request->vehicle_id);
            $extra_charge = ExtraCharge::where('city_id', $request->city_id)
                ->whereStatus(1)
                ->get();

            $vehicle_allow = appSettingcurrency('is_vehicle_in_order');
            $insurance_allow = SettingData('insurance_allow', 'insurance_allow');
            $insurance_value = SettingData('insurance_percentage', 'insurance_percentage');

            $weight_difference = $request->total_weight > $city->min_weight
                ? ($request->total_weight - $city->min_weight)
                : 0;

            $min_weight = $weight_difference * $city->per_weight_charges;

            if($vehicle){
                $distance_difference = $request->total_distance > $vehicle->min_km
                ? ($request->total_distance - $vehicle->min_km)
                : 0;
            }else{
                $distance_difference = $request->total_distance > $city->min_distance
                    ? ($request->total_distance - $city->min_distance)
                    : 0;

                    if($vehicle){
                        $min_distance = 0;
                    }else{
                        $min_distance = $distance_difference * $city->per_distance_charges;
                    }
            }


            if ($vehicle != '' && $vehicle_allow && $request->vehicle_id) {
                $vehicle_details = $request->total_distance > $vehicle->min_km ? ($request->total_distance - $vehicle->min_km) * $vehicle->per_km_charge : $vehicle->price;
            }

            if ($insurance_allow && $request->is_insurance) {
                $insurance = $request->insurance_amount * $insurance_value / 100;
            }

            $totalAmount = $min_weight + $min_distance + $vehicle_details + $insurance + $city->fixed_charges;

            foreach ($extra_charge as $charges) {
                if ($charges->charges_type == "fixed") {
                    $total_fixed += $charges->charges;
                } else {
                    $total_percentage += $charges->charges * $totalAmount / 100;
                }
            }

            $totalAmountAll = $total_percentage + $total_fixed + $totalAmount;
        }

        return json_custom_response([
            'fixed_amount' => $city->fixed_charges ?? 0,
            'weight_amount' => $min_weight,
            'distance_amount' => $min_distance,
            'extra_charges' => $extra_charge,
            'vehicle_amount' => $vehicle_details,
            'insurance_amount' => $insurance,
            'diff_weight' => $weight_difference,
            'diff_distance' => $distance_difference,
            'total_amount' =>(float) $totalAmountAll,
            'base_total' => $totalAmount,
        ]);
    }
    public function orderPrintList(Request $request)
    {
        $orderprint = Order::whereIn('status', [ 'courier_departed', 'courier_picked_up', 'courier_assigned','courier_arrived','active','create']);
        if ($request->has('status') && isset($request->status)) {
            if (request('status') == 'trashed') {
                $order = $orderprint->withTrashed();
            } else {
                $order = $orderprint->where('status', request('status'));
            }
        };
        $per_page = config('constant.PER_PAGE_LIMIT');
        if ($request->has('per_page') && !empty($request->per_page)) {
            if (is_numeric($request->per_page)) {
                $per_page = $request->per_page;
            }
            if ($request->per_page == -1) {
                $per_page = $orderprint->count();
            }
        }
        $orderprint = $orderprint->orderBy('date', 'desc')->paginate($per_page);
        $items = OrderPrintResource::collection($orderprint);
        $response = [
            'pagination' => json_pagination_response($items),
            'data' => $items,
        ];

        return json_custom_response($response);
    }
    public function OrderBidList(Request $request)
    {
        $orderbid = OrderBid::myBid()
            ->whereHas('order', function ($query) {
                $query->where('status', '!=', 'cancelled');
            })
            ->orderBy('id', 'desc')
            ->get();
    
        $items = OrderBidResource::collection($orderbid);
    
        $response = [
            'data' => $items,
        ];
    
        return json_custom_response($response);
    }
    public function orderStatusUpdate(Request $request,$id)
    {
        $request->validate([
            'status' => 'required',
        ]);

        $order = Order::findOrFail($id);
        $order->status = $request->input('status');
        $order->save();
        $message = __('message.status_updated');
        if(request()->is('api/*')){
            return json_message_response( $message );
        }
        return redirect()->back()->with('success', __('message.status_updated'));
    }
    public function transferDeliverymanOrder($id)
    {
        $order = Order::find($id);
        $deliveryMenQuery = User::where('city_id', $order->city_id)
            ->where('status', 1)
            ->where('user_type', 'delivery_man')
            ->where(function ($query) {
                $query->whereNotNull('email_verified_at')
                    ->whereNotNull('otp_verify_at')
                    ->whereNotNull('document_verified_at');
            });
        $deliveryMen = $deliveryMenQuery->get();
        return response()->json([
                    'message' => 'Order successfully transferred to the delivery person',
                    'delivery_person' =>  $deliveryMen,
                ]);
    }
}
