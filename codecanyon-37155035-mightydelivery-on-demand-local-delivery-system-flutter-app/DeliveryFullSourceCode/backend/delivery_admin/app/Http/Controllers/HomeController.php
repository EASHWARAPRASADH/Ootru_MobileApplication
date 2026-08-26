<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AppSetting;
use App\Models\Country;
use App\Models\User;
use App\Models\City;
use App\Models\Document;
use App\Models\Order;
use App\Http\Resources\OrderResource;
use App\Http\Resources\WithdrawRequestResource;
use App\Models\Coupon;
use App\Models\CourierCompanies;
use App\Models\DefaultKeyword;
use App\Models\DeliveryManDocument;
use App\Models\DeliveryManSection;
use App\Models\ExtraCharge;
use App\Models\FrontendData;
use App\Models\LanguageDefaultList;
use App\Models\LanguageList;
use App\Models\Pages;
use App\Models\Wallet;
use App\Models\WalletHistory;
use App\Models\PaymentGateway;
use App\Models\Payment;
use App\Models\Screen;
use App\Models\Setting;
use App\Models\UserBankAccount;
use App\Models\Vehicle;
use App\Models\WithdrawRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Models\UserAddress;
use App\Models\WebsiteSection;
use Illuminate\Support\Facades\Config;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /*
     * Dashboard Pages Routes
     */
    public function index(Request $request)
    {
        $auth_user = auth()->user();
        $params = [
            'from_date' => request('from_date') ?? null,
            'to_date' => request('to_date') ?? null,
            'created_at' => request('created_at') ?? null,
        ];
        $ordersQuery = Order::myOrder();
        if ($params['from_date'] && $params['to_date']) {
            if ($params['from_date'] == $params['to_date']) {
                $ordersQuery->whereDate('created_at', '=', [$params['from_date']]);
            } else {
                $ordersQuery->whereBetween('created_at', [$params['from_date'], $params['to_date']]);
            }
        }
        $userQuery = User::query();
        if ($params['from_date'] && $params['to_date']) {
            if ($params['from_date'] == $params['to_date']) {
                $userQuery->whereDate('created_at', '=', [$params['from_date']]);
            } else {
                $userQuery->whereBetween('created_at', [$params['from_date'], $params['to_date']]);
            }
        }
        $deliverymanQuery = User::query();
        if ($params['from_date'] && $params['to_date']) {
            if ($params['from_date'] == $params['to_date']) {
                $deliverymanQuery->whereDate('created_at', '=', [$params['from_date']]);
            } else {
                $deliverymanQuery->whereBetween('created_at', [$params['from_date'], $params['to_date']]);
            }
        }

        $recent_order = $ordersQuery->whereDate('date', '<=', Carbon::now()->format('Y-m-d'))->whereNotIn('status', ['pending'])->orderBy('date', 'desc')->paginate(10);
        $data['recent_order'] = OrderResource::collection($recent_order);

        $paymentQuery = Payment::query();
        if ($params['from_date'] && $params['to_date']) {
            if ($params['from_date'] == $params['to_date']) {
                $paymentQuery->whereDate('created_at', '=', [$params['from_date']]);
            } else {
                $paymentQuery->whereBetween('created_at', [$params['from_date'], $params['to_date']]);
            }
        }

        $wallteQuery = Wallet::query();
        if ($params['from_date'] && $params['to_date']) {
            if ($params['from_date'] == $params['to_date']) {
                $wallteQuery->whereDate('created_at', '=', [$params['from_date']]);
            } else {
                $wallteQuery->whereBetween('created_at', [$params['from_date'], $params['to_date']]);
            }
        }
        $ordersQuery = Order::myOrder();

        if ($params['from_date'] && $params['to_date']) {
            if ($params['from_date'] == $params['to_date']) {
                $ordersQuery->whereDate('created_at', '=', [$params['from_date']]);
            } else {
                $ordersQuery->whereBetween('created_at', [$params['from_date'], $params['to_date']]);
            }
        }
        $ordersQuery = Order::myOrder();

        if ($params['from_date'] && $params['to_date']) {
            if ($params['from_date'] == $params['to_date']) {
                $ordersQuery->whereDate('created_at', '=', [$params['from_date']]);
            } else {
                $ordersQuery->whereBetween('created_at', [$params['from_date'], $params['to_date']]);
            }
        }
        $totalOrdersQuery = clone $ordersQuery;
        $totalCreateOrderQuery = clone $ordersQuery;
        $totalAcceptedOrderQuery = clone $ordersQuery;
        $totalAssignedOrderQuery = clone $ordersQuery;
        $totalArrivedOrderQuery = clone $ordersQuery;
        $totalPickupOrderQuery = clone $ordersQuery;
        $totalDepartedOrderQuery = clone $ordersQuery;
        $totalDeliveredOrderQuery = clone $ordersQuery;
        $totalCancelledOrderQuery = clone $ordersQuery;

        $statuses = ['courier_assigned', 'active', 'courier_arrived', 'courier_departed', 'courier_picked_up'];
        $data['dashboard'] = [
            'total_order' => $totalOrdersQuery->count(),
            'total_create_order' => $totalCreateOrderQuery->where('status', 'create')->count(),
            'total_accepetd_order' => $totalAcceptedOrderQuery->where('status', 'active')->count(),
            'total_assigned_order' => $totalAssignedOrderQuery->where('status', 'courier_assigned')->count(),
            'total_arrived_order' => $totalArrivedOrderQuery->where('status', 'courier_arrived')->count(),
            'total_pickup_order' => $totalPickupOrderQuery->where('status', 'courier_picked_up')->count(),
            'total_departed_order' => $totalDepartedOrderQuery->where('status', 'courier_departed')->count(),
            'total_delivered_order' => $totalDeliveredOrderQuery->where('status', 'completed')->count(),
            'total_user' => $userQuery->where('user_type', 'client')->count(),
            'total_delivery_person' => $deliverymanQuery->where('user_type', 'delivery_man')->count(),
            'total_cancelled_order' => $totalCancelledOrderQuery->where('status', 'cancelled')->count(),
            'total_order_today' => Order::myOrder()->today()->count(),
            'total_order_today_peding' => Order::myOrder()->today()->where('status', 'create')->count(),
            'total_order_today_inprogress' => Order::myOrder()->today()->whereIn('status', $statuses)->count(),
            'total_order_today_completed' => Order::myOrder()->today()->where('status', 'completed')->count(),
            'total_order_today_cancelled' => Order::myOrder()->today()->where('status', 'cancelled')->count(),
            'total_collection_by_order' => Payment::sum('total_amount'),
            'total_admin_comission' => Payment::sum('admin_commission'),
            'total_delivery_comission' => Payment::sum('delivery_man_commission'),
            'total_wallet_balance' => Wallet::sum('total_amount'),
            'monthly_payment_count' => Payment::whereMonth('created_at', date('m'))
                ->whereYear('created_at', date('Y'))
                ->sum('total_amount'),

        ];

        $withdrawRequestQuery = WithdrawRequest::select();
        if ($params['from_date'] && $params['to_date']) {
            if ($params['from_date'] == $params['to_date']) {
                $withdrawRequestQuery->whereDate('created_at', '=', [$params['from_date']]);
            } else {
                $withdrawRequestQuery->whereBetween('created_at', [$params['from_date'], $params['to_date']]);
            }
        }

        $withdrawrequest = $withdrawRequestQuery->orderBy('created_at', 'desc')->paginate(10);
        $data['recent_withdrawrequest'] = WithdrawRequestResource::collection($withdrawrequest);

        //monthly payment and order  count
        $paramsChat = [
            'from_date' => !empty($params['from_date']) ? Carbon::parse($params['from_date']) : null,
            'to_date' => !empty($params['to_date']) ? Carbon::parse($params['to_date']) : null,
        ];

        if (!empty($paramsChat['from_date']) && !empty($paramsChat['to_date'])) {
            $month_start = $paramsChat['from_date'];
            $today = $paramsChat['to_date'];
        } else {
            $month_start = Carbon::now()->startOfMonth();
            $today = Carbon::now();
        }

        $diff = $month_start->diffInDays($today) + 1;

        $data['monthlist'] = [
            'month_start' => $month_start,
            'month_end' => $today,
            'diff' => $diff,
        ];
        $paymentQuery = Payment::myPayment();

        if ($params['from_date'] && $params['to_date']) {
            if ($params['from_date'] == $params['to_date']) {
                $withdrawRequestQuery->whereDate('created_at', '=', [$params['from_date']]);
            } else {
                $withdrawRequestQuery->whereBetween('created_at', [$params['from_date'], $params['to_date']]);
            }
        }

        $monthly_order_count = Order::myOrder()->selectRaw('DATE_FORMAT(created_at , "%w") as days , DATE_FORMAT(created_at , "%Y-%m-%d") as date')
            ->whereBetween('created_at', [$month_start, $today])
            ->get()->toArray();

        $monthly_order_count_data = [];

        $order_collection = collect($monthly_order_count);

        $monthly_payment_report = $paymentQuery->selectRaw('DATE_FORMAT(created_at , "%w") as days , DATE_FORMAT(created_at , "%Y-%m-%d") as date, total_amount ')
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$month_start, $today])
            ->whereHas('order', function ($query) {
                $query->where('status', 'completed');
            })->withTrashed()
            ->get()->toArray();

        $monthly_payment_completed_order_data = [];

        $payment_collection = collect($monthly_payment_report);

        $monthly_payment_cancelled_report = $paymentQuery->selectRaw('DATE_FORMAT(created_at , "%w") as days , DATE_FORMAT(created_at , "%Y-%m-%d") as date, cancel_charges ')
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$month_start, $today])
            ->whereHas('order', function ($query) {
                $query->where('status', 'cancelled');
            })->withTrashed()
            ->get()->toArray();

        $monthly_payment_cancelled_order_data = [];
        $payment_cancelled_collection = collect($monthly_payment_cancelled_report);

        for ($i = 0; $i < $diff; $i++) {
            $total = $order_collection->filter(function ($value, $key) use ($month_start, $i) {
                return $value['date'] == date('Y-m-d', strtotime($month_start . ' + ' . $i . 'day'));
            })->count();

            $monthly_order_count_data[] = [
                'total' => $total,
                'date' => date('Y-m-d', strtotime($month_start . ' + ' . $i . 'day')),
            ];

            $total_amount = $payment_collection->filter(function ($value, $key) use ($month_start, $i) {
                return $value['date'] == date('Y-m-d', strtotime($month_start . ' + ' . $i . 'day'));
            })->sum('total_amount');

            $monthly_payment_completed_order_data[] = [
                'total_amount' => $total_amount,
                'date' => date('Y-m-d', strtotime($month_start . ' + ' . $i . 'day')),
            ];

            $cancel_charges = $payment_cancelled_collection->filter(function ($value, $key) use ($month_start, $i) {
                return $value['date'] == date('Y-m-d', strtotime($month_start . ' + ' . $i . 'day'));
            })->sum('cancel_charges');

            $monthly_payment_cancelled_order_data[] = [
                'total_amount' => $cancel_charges,
                'date' => date('Y-m-d', strtotime($month_start . ' + ' . $i . 'day')),
            ];
        }
        $data['monthly_order_count'] = $monthly_order_count_data;
        $data['completed'] = $monthly_payment_completed_order_data;
        $data['cancelled'] = $monthly_payment_cancelled_order_data;

        // weekly order count
        $sunday = strtotime('sunday -1 week');
        $sunday = date('w', $sunday) === date('w') ? $sunday + 7 * 86400 : $sunday;
        $saturday = strtotime(date('Y-m-d', $sunday) . ' +6 days');
        if (!empty($paramsChat['from_date']) && !empty($paramsChat['to_date'])) {
            $week_start = $paramsChat['from_date'];
            $week_end = $paramsChat['to_date'];
        } else {
            $week_start = date('Y-m-d 00:00:00', $sunday);
            $week_end = date('Y-m-d 23:59:59', $saturday);
        }

        $dashboard_data['week'] = [
            'week_start' => $week_start,
            'week_end' => $week_end
        ];
        $ordersQuery = Order::query();

        if ($params['from_date'] && $params['to_date']) {
            if ($params['from_date'] == $params['to_date']) {
                $ordersQuery->whereDate('created_at', '=', [$params['from_date']]);
            } else {
                $ordersQuery->whereBetween('created_at', [$params['from_date'], $params['to_date']]);
            }
        }
        $weekly_order_count = $ordersQuery->selectRaw('DATE_FORMAT(created_at , "%w") as days , DATE_FORMAT(created_at , "%Y-%m-%d") as date')
            ->whereBetween('created_at', [$week_start, $week_end])
            ->get()->toArray();

        $order_data = [];

        $order_collection = collect($weekly_order_count);
        for ($i = 0; $i < 7; $i++) {
            $total = $order_collection->filter(function ($value, $key) use ($week_start, $i) {
                return $value['date'] == date('Y-m-d', strtotime($week_start . ' + ' . $i . 'day'));
            })->count();

            $order_data[] = [
                'day' => date('l', strtotime($week_start . ' + ' . $i . 'day')),
                'total' => $total,
                'date' => date('Y-m-d', strtotime($week_start . ' + ' . $i . 'day')),
            ];
        }

        $cityQuery = City::where('status', 1);
        if ($params['from_date'] && $params['to_date']) {
            if ($params['from_date'] == $params['to_date']) {
                $withdrawRequestQuery->whereDate('created_at', '=', [$params['from_date']]);
            } else {
                $withdrawRequestQuery->whereBetween('created_at', [$params['from_date'], $params['to_date']]);
            }
        }

        $ordersQuery = Order::query();
        if ($params['from_date'] && $params['to_date']) {
            if ($params['from_date'] == $params['to_date']) {
                $ordersQuery->whereDate('created_at', '=', [$params['from_date']]);
            } else {
                $ordersQuery->whereBetween('created_at', [$params['from_date'], $params['to_date']]);
            }
        }

        $cityList = $cityQuery->get();

        $cityOrders = $ordersQuery->selectRaw('city_id,
                                        COUNT(*) as total,
                                        SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as delivered,
                                        SUM(CASE WHEN status = "cancelled" THEN 1 ELSE 0 END) as cancelled,
                                        SUM(CASE WHEN status IN ("courier_assigned", "courier_arrived", "create", "courier_picked_up", "active", "courier_departed") THEN 1 ELSE 0 END) as in_progress')
            ->groupBy('city_id')
            ->get()
            ->keyBy('city_id');

        $cityData = $cityList->map(function ($city) use ($cityOrders) {
            $orders = $cityOrders->get($city->id, (object) [
                'total' => 0,
                'delivered' => 0,
                'cancelled' => 0,
                'in_progress' => 0
            ]);

            return [
                'city' => $city->name,
                'count' => $orders->total,
                'color' => '#' . substr(md5($city->name), 0, 6),
                'in_progress' => $orders->in_progress,
                'delivered' => $orders->delivered,
                'cancelled' => $orders->cancelled
            ];
        });
        $cityData = collect($cityData)->sortByDesc('count')->values()->all();
        $data['weekly_order_count'] = $order_data;
        $weekly_count = array_column($data['weekly_order_count'], "total");

        if (auth()->user()->user_type == 'client') {
            return redirect()->route('order.create');
        }

        return view('dashboards.admin-dashboard', compact('auth_user', 'data', 'weekly_count', 'cityData', 'cityList', 'params'));
    }

    public function highDemanding_areas(Request $request)
    {
        $now = Carbon::now();
        $distanceLimit = AppSetting::first()->distance;

        // Get all orders for the day (change to today or yesterday as needed)
        $orders = Order::where('status', 'create')
            ->whereDate('created_at', $now->toDateString())
            ->get();

        $groupedOrders = collect();
        if ($orders->isNotEmpty()) {
            $groupedOrders = $orders->groupBy(function ($order) {
                $pickup = $order->pickup_point;
                if (!is_array($pickup)) {
                    $pickup = json_decode($pickup, true);
                }
                if (isset($pickup['latitude'], $pickup['longitude'])) {
                    return round((float) $pickup['latitude'], 3) . '_' . round((float) $pickup['longitude'], 3);
                }
                return 'unknown_location';
            });
        }

        // All active delivery men
        $drivers = User::where(['user_type' => 'delivery_man', 'status' => 1])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        // Compute zones
        $zones = $groupedOrders->map(function ($ordersInZone, $zoneKey) use ($drivers, $distanceLimit) {
            [$lat, $lng] = explode('_', $zoneKey);
            $lat = (float) $lat;
            $lng = (float) $lng;

            // Filter drivers by proximity
            $driversInZone = $drivers->filter(function ($driver) use ($lat, $lng, $distanceLimit) {
                $distance = 6371 * acos(
                    cos(deg2rad($lat)) * cos(deg2rad($driver->latitude)) *
                    cos(deg2rad($driver->longitude) - deg2rad($lng)) +
                    sin(deg2rad($lat)) * sin(deg2rad($driver->latitude))
                );
                return $distance <= $distanceLimit;
            });

            $orderCount = $ordersInZone->count();
            $availableDrivers = $driversInZone->count();

            /** Old Static Zone calculation */
            // $halfordercount = $orderCount / 2;

            // $level = match (true) {
            //     $orderCount == 1 => 'low', //green
            //     $orderCount == $availableDrivers => 'normal',
            //     $halfordercount == $availableDrivers, $halfordercount < $availableDrivers => 'moderate',
            //     $halfordercount > $availableDrivers => 'high',
            //     default => 'low',
            // };

            // Get dynamic thresholds from setting
            $setting = Config::get('highdemand-settings');
            $settingKeys = [];
            foreach ($setting as $k => $s) {
                foreach ($s as $sk => $ss) {
                    $settingKeys[] = $k . '_' . $sk;
                }
            }

            $setting_value = Setting::whereIn('key', $settingKeys)->pluck('value', 'key');

            // Dynamic percentages
            $redPercent = (float) ($setting_value['MAP_RED'] ?? 50);
            $orangePercent = (float) ($setting_value['MAP_ORANGE'] ?? 30);
            $yellowPercent = (float) ($setting_value['MAP_YELLOW'] ?? 20);

            // Thresholds based on total order count
            $redThreshold = ($orderCount * $redPercent) / 100;
            $orangeThreshold = ($orderCount * $orangePercent) / 100;
            $yellowThreshold = ($orderCount * $yellowPercent) / 100;

            // Determine demand level based on available driver count
            $level = 'low';
            if ($orderCount == 1) {
            } else if ($availableDrivers < round($redThreshold)) {
                $level = 'high';
            } elseif ($availableDrivers > round($orangeThreshold)) {
                $level = 'moderate';
            } elseif ($availableDrivers = round($yellowThreshold)) {
                $level = 'normal';
            } else {
                $level = 'low';
            }

            return [
                'lat' => $lat,
                'lng' => $lng,
                'level' => $level,
                'order_count' => $orderCount,
                'driver_count' => $availableDrivers,
            ];
        })->values();

        // Mark busy riders
        $busyRiderIds = Order::whereIn('status', ['pending', 'processing', 'assigned'])
            ->pluck('delivery_man_id')
            ->filter()
            ->unique()
            ->toArray();

        $activeRiders = $drivers->map(function ($rider) use ($busyRiderIds) {
            return [
                'id' => $rider->id,
                'name' => $rider->name,
                'lat' => $rider->latitude,
                'lng' => $rider->longitude,
                'is_available' => !in_array($rider->id, $busyRiderIds),
            ];
        })->values();

        $cities = City::where('status', 1)->get(['id', 'name', 'country_id'])->toArray();
        $countries = Country::where('status', 1)->get(['id', 'name'])->toArray();

        return view('live-order-tracking.high-demand-areas', [
            'cities' => $cities,
            'countries' => $countries,
            'zones' => $zones,
            'riders' => $activeRiders,
            'assets' => ['location']
        ]);
    }


    public function changeLanguage($locale)
    {
        App::setLocale($locale);
        session()->put('locale', $locale);
        return redirect()->back();
    }

    /*
     * Auth pages Routs
     */

    function authLogin()
    {
        return view('auth.login');
    }

    function authRegister()
    {
        $assets = ['phone'];
        return view('auth.register', compact('assets'));
    }

    function authRecoverPassword()
    {
        return view('auth.forgot-password');
    }

    function authConfirmEmail()
    {
        return view('auth.verify-email');
    }

    function authLockScreen()
    {
        return view();
    }

    public function changeStatus(Request $request)
    {
        $type = $request->type;
        $message_form = "";
        $message = __('message.update_form', ['form' => __('message.status')]);
        switch ($type) {
            case 'role':
                $role = \App\Models\Role::find($request->id);
                $role->status = $request->status;
                $role->save();
                break;

            case 'user':
                $user = User::find($request->id);
                if ($request->status != null) {
                    $user->status = $request->status;
                }

                if ($request->has('is_autoverified_email')) {
                    $isAutoVerified = $request->input('is_autoverified_email') == '1';

                    $user->is_autoverified_email = $isAutoVerified ? 1 : 0;
                    $user->email_verified_at = $isAutoVerified ? now() : null;
                }


                if ($request->has('is_autoverified_mobile')) {
                    $isAutoVerifiedMobile = $request->input('is_autoverified_mobile') == '1';

                    $user->is_autoverified_mobile = $isAutoVerifiedMobile ? 1 : 0;
                    $user->otp_verify_at = $isAutoVerifiedMobile ? now() : null;
                }


                if ($request->has('is_autoverified_document')) {
                    $isAutoVerifiedDocument = $request->input('is_autoverified_document') === '1';

                    $user->is_autoverified_document = $isAutoVerifiedDocument ? 1 : 0;
                    $user->document_verified_at = $isAutoVerifiedDocument ? ($user->document_verified_at ?? now()) : null;

                    $verificationStatus = $isAutoVerifiedDocument ? '1' : '2';

                    DeliveryManDocument::where('delivery_man_id', $request->id)
                        ->update(['is_verified' => $verificationStatus]);
                }


                $user->save();
                if ($user->status == 0) {
                    $user->tokens()->delete();
                }
                break;


            case 'pages':
                $user = Pages::find($request->id);
                $status = $request->status == 0 ? '0' : '1';
                $user->status = $status;
                $user->save();
                break;

            case 'document':
                $document = Document::find($request->id);
                $status = $request->status == 0 ? '0' : '1';
                $document->status = $status;
                $document->save();
                break;

            case 'coupon':
                $document = Coupon::find($request->id);
                $status = $request->status == 0 ? '0' : '1';
                $document->status = $status;
                $document->save();
                break;

            case 'country':
                $country = Country::find($request->id);
                $status = $request->status == 0 ? '0' : '1';
                $country->status = $status;
                $country->save();
                break;

            case 'city':
                $city = City::find($request->id);
                $status = $request->status == 0 ? '0' : '1';
                $city->status = $status;
                $city->save();
                break;

            case 'vehicle':
                $vehicle = Vehicle::find($request->id);
                $status = $request->status == 0 ? '0' : '1';
                $vehicle->status = $status;
                $vehicle->save();
                break;

            case 'extracharge':
                $extracharge = ExtraCharge::find($request->id);
                $status = $request->status == 0 ? '0' : '1';
                $extracharge->status = $status;
                $extracharge->save();
                break;

            case 'couriercompanies':
                $couriercompanies = CourierCompanies::find($request->id);
                $status = $request->status == 0 ? '0' : '1';
                $couriercompanies->status = $status;
                $couriercompanies->save();
                break;
            default:
                $message = 'error';
                break;
        }

        if ($message_form != null) {
            $message = __('message.added_form', ['form' => $message_form]);
            if ($request->status == 0) {
                $message = __('message.remove_form', ['form' => $message_form]);
            }
        }

        return json_custom_response(['message' => $message, 'status' => true]);
    }

    public function changeVerify(Request $request)
    {
        $type = $request->type;
        $message = "";
        switch ($type) {
            case 'verify':
                $user = User::find($request->id);
                if ($request->otp_verify_at) {
                    $user->otp_verify_at = now();
                } else {
                    $user->otp_verify_at = null;
                }
                $user->save();
                break;
            case 'verify_delivery_men':
                $user = User::find($request->id);
                if ($request->otp_verify_at) {
                    $user->otp_verify_at = now();
                } else {
                    $user->otp_verify_at = null;
                }
                $user->save();
                break;
            default:
                $message = 'Invalid operation';
                break;
        }

        if ($message === "") {
            $message = __('message.Verify');
        }

        return response()->json(['message' => $message, 'status' => true]);
    }


    public function getAjaxList(Request $request)
    {
        $items = array();
        $value = $request->q;
        $auth_user = authSession();
        switch ($request->type) {
            case 'permission':
                $items = \App\Models\Permission::select('id', 'name as text')->whereNull('parent_id');
                if ($value != '') {
                    $items->where('name', 'LIKE', $value . '%');
                }
                $items = $items->get();
                break;

            case 'timezone':
                $items = timeZoneList();
                foreach ($items as $k => $v) {
                    if ($value != '') {
                        if (strpos($v, $value) !== false) {
                        } else {
                            unset($items[$k]);
                        }
                    }
                }
                $data = [];
                $i = 0;
                foreach ($items as $key => $row) {
                    $data[$i] = [
                        'id' => $key,
                        'text' => $row,
                    ];
                    $i++;
                }
                $items = $data;
                break;

            case 'country-list';
                $items = Country::select('id', 'name as text')->where('status', 1);
                if ($value != '') {
                    $items->where('name', 'LIKE', '%' . $value . '%');
                }
                $items = $items->get();
                break;

            case 'city-list';
                $items = City::select('id', 'name as text')->where('status', 1);
                if ($value != '') {
                    $items->where('name', 'LIKE', '%' . $value . '%');
                }
                $items = $items->get();
                break;

            case 'city-list-filter':
                $country_id = $request->input('country_id');
                $items = City::select('id', 'name as text');

                if ($country_id != 'null') {
                    $items->where('country_id', $country_id);
                }

                if (!empty($value)) {
                    $items->where('name', 'LIKE', '%' . $value . '%');
                }

                $items = $items->get();
                break;



            case 'vehicle-list':
                $cityId = $request->query("city_id");
                $items = Vehicle::select('id', 'title as text', 'min_km', 'per_km_charge', 'price')
                    ->where('status', 1)
                    ->whereJsonContains('city_ids', $cityId)
                    ->Orwhere('type', 'all');
                if (!empty($value)) {
                    $items->where('title', 'LIKE', '%' . $value . '%');
                }
                $items = $items->get();
                break;

            case 'extra_charge_city':
                $classIds = request('country_id');
                $items = City::select('id', 'name as text')->where('country_id', $classIds);
                if ($value !== '') {
                    $items->where('name', 'LIKE', '%' . $value . '%');
                }
                $items = $items->get();
                break;
            case 'country_base_city':
                $classIds = request('country_id');
                $items = City::select('id', 'name as text', 'fixed_charges', 'per_weight_charges', 'per_distance_charges', 'min_weight', 'min_distance')->where('country_id', $classIds);
                if ($value !== '') {
                    $items->where('name', 'LIKE', '%' . $value . '%');
                }
                $items = $items->with('extraCharges')->get();

                break;

            case 'deliveryman_name':
                $items = User::select('id', 'name as text')->where('user_type', 'delivery_man')->whereStatus(1);
                if ($value != '') {
                    $items->where('name', 'LIKE', '%' . $value . '%');
                }
                $items = $items->get();
                break;
            case 'client_name':
                $items = User::select('id', 'name as text')->where('user_type', 'client')->whereStatus(1);
                if ($value != '') {
                    $items->where('name', 'LIKE', '%' . $value . '%');
                }
                $items = $items->get();
                break;
            case 'document_name':
                $items = Document::select('id', 'name as text');
                if ($value != '') {
                    $items->where('name', 'LIKE', '%' . $value . '%');
                }
                $items = $items->get();
                break;
            case 'screen':
                $items = Screen::select('screenId', 'screenName as text');
                if ($value != '') {
                    $items->where('screenName', 'LIKE', '%' . $value . '%');
                }
                $items = $items->get()->map(function ($screen_id) {
                    return ['id' => $screen_id->screenId, 'text' => $screen_id->text];
                });
                $items = $items;
                break;
            case 'language-list-data':
                $languageId = $request->id;
                $items = LanguageDefaultList::where('id', $languageId);
                $items = $items->first();
                break;
            case 'languagelist':
                $data = LanguageList::pluck('language_id')->toArray();
                $items = LanguageDefaultList::whereNotIn('id', $data)->select('id', 'languageName as text');
                if ($value != '') {
                    $items->where('languageName', 'LIKE', '%' . $value . '%');
                }
                $items = $items->get();
                break;
            case 'defaultkeyword':
                $items = DefaultKeyword::select('id', 'keyword_name as text');
                if ($value != '') {
                    $items->where('keyword_name', 'LIKE', '%' . $value . '%');
                }
                $items = $items->get();
                break;
            case 'languagetable':
                $items = LanguageList::select('id', 'language_name as text')->where('status', 1);
                if ($value != '') {
                    $items->where('language_name', 'LIKE', '%' . $value . '%');
                }
                $items = $items->get();
                break;

            case 'page-list';
                $items = Pages::select('id', 'title as text');
                if ($value != '') {
                    $items->where('name', 'LIKE', '%' . $value . '%');
                }
                $items = $items->get();
                break;

            case 'couriercompany-list';
                $items = CourierCompanies::select('id', 'name as text')->where('status', 1);
                if ($value != '') {
                    $items->where('name', 'LIKE', '%' . $value . '%');
                }
                $items = $items->get();
                break;
            default:
                break;
        }
        return response()->json(['status' => true, 'results' => $items]);
    }

    public function removeFile(Request $request)
    {
        $type = $request->type;
        $data = null;

        switch ($type) {

            case 'gateway_image':
                $data = PaymentGateway::find($request->id);
                $message = __('message.msg_removed', ['name' => __('message.paymentgateway')]);
                break;
            case 'language_image':
                $data = LanguageList::find($request->id);
                $message = __('message.msg_removed', ['name' => __('message.slider')]);
                break;
            case 'frontend_data_image':
                $data = FrontendData::find($request->id);
                $message = __('message.msg_removed', ['name' => __('message.frontend_data')]);
                break;
            case 'deliverypartner':
                $data = FrontendData::find($request->id);
                $message = __('message.msg_removed', ['name' => __('message.frontend_data')]);
                break;
            case 'frontend_images':
                $data = Setting::find($request->id);
                $type = request('sub_type');
                $message = __('message.msg_removed', ['name' => __('message.' . $type)]);
                break;
            case 'attachment_resolve_file':
                $data = Media::find($request->id);
                $data->delete();
                $data = null;
                $type = $request->id;
                $message = __('message.msg_removed', ['name' => __('message.attachment_file')]);
                break;
            case 'order_status_section':
                $data = FrontendData::find($request->id);
                $message = __('message.msg_removed', ['name' => __('message.order_status_section_image')]);
                break;
            case 'section_image':
                $data = WebsiteSection::find($request->id);
                $message = __('message.msg_removed',[ 'name' => __('message.section')]);
                break;
            case 'delivery_man_section_image':
                $data = DeliveryManSection::find($request->id);
                $message = __('message.msg_removed',[ 'name' => __('message.delivery_man_image')]);
                break;
            default:
                $data = AppSetting::find($request->id);
                $message = __('message.msg_removed', ['name' => __('message.image')]);
                break;
        }

        if ($data != null) {
            $data->clearMediaCollection($type);
        }

        $response = [
            'status' => true,
            'id' => $request->id,
            'image' => getSingleMedia($data, $type),
            'preview' => $type . "_preview",
            'message' => $message
        ];
        return json_custom_response($response);
    }

    public function destroySelected(Request $request)
    {

        $checked_ids = $request->datatable_checked_ids;
        $types = $request->datatable_button_title;
        $data = null;

        switch ($types) {
            case 'city-checked':
                foreach ($checked_ids as $id) {
                    $city = City::withTrashed()->where('id', $id)->first();
                    if ($city) {
                        if (env('APP_DEMO')) {
                            $message = __('message.demo_permission_denied');
                            if (request()->ajax()) {
                                return response()->json(['status' => false, 'message' => $message, 'event' => 'validation']);
                            }
                        }
                        if ($city->deleted_at != null) {
                            $city->forceDelete();
                        } else {
                            $city->delete();
                        }
                    }
                    $message = __('message.delete_form', ['form' => __('message.city')]);
                }
                break;
            case 'country-checked':
                foreach ($checked_ids as $id) {
                    $country = Country::withTrashed()->where('id', $id)->first();
                    if ($country) {
                        if (env('APP_DEMO')) {
                            $message = __('message.demo_permission_denied');
                            if (request()->ajax()) {
                                return response()->json(['status' => false, 'message' => $message, 'event' => 'validation']);
                            }
                        }
                        if ($country->deleted_at != null) {
                            $country->forceDelete();
                        } else {
                            $country->delete();
                        }
                    }
                    $message = __('message.delete_form', ['form' => __('message.country')]);
                }
                break;

            case 'users-checked':
                foreach ($checked_ids as $id) {
                    $user = user::withTrashed()->where('id', $id)->first();
                    if ($user) {
                        if (env('APP_DEMO')) {
                            $message = __('message.demo_permission_denied');
                            if (request()->ajax()) {
                                return response()->json(['status' => false, 'message' => $message, 'event' => 'validation']);
                            }
                        }
                        if ($user->deleted_at != null) {
                            $user->forceDelete();
                        } else {
                            $user->delete();
                        }
                    }
                    $message = __('message.delete_form', ['form' => __('message.user')]);
                }
                break;

            case 'order-checked':
                foreach ($checked_ids as $id) {
                    $order = Order::withTrashed()->where('id', $id)->first();
                    if ($order) {
                        if (env('APP_DEMO')) {
                            $message = __('message.demo_permission_denied');
                            if (request()->ajax()) {
                                return response()->json(['status' => false, 'message' => $message, 'event' => 'validation']);
                            }
                        }
                        if ($order->deleted_at != null) {
                            $order->forceDelete();
                        } else {
                            $order->delete();
                        }
                    }
                    $message = __('message.delete_form', ['form' => __('message.order')]);
                }
                break;

            case 'deliveryman-checked':
                foreach ($checked_ids as $id) {
                    $user = User::withTrashed()->where('id', $id)->first();
                    if ($user) {
                        if (env('APP_DEMO')) {
                            $message = __('message.demo_permission_denied');
                            if (request()->ajax()) {
                                return response()->json(['status' => false, 'message' => $message, 'event' => 'validation']);
                            }
                        }
                        if ($user->deleted_at != null) {
                            $user->forceDelete();
                        } else {
                            $user->delete();
                        }
                    }
                    $message = __('message.delete_form', ['form' => __('message.delivery_man')]);
                }
                break;
            case 'deliverymandocument-checked':
                foreach ($checked_ids as $id) {
                    $deliverymendocument = DeliveryManDocument::withTrashed()->where('id', $id)->first();
                    if ($deliverymendocument) {
                        if (env('APP_DEMO')) {
                            $message = __('message.demo_permission_denied');
                            if (request()->ajax()) {
                                return response()->json(['status' => false, 'message' => $message, 'event' => 'validation']);
                            }
                        }
                        if ($deliverymendocument->deleted_at != null) {
                            $deliverymendocument->forceDelete();
                        } else {
                            $deliverymendocument->delete();
                        }
                    }
                    $message = __('message.delete_form', ['form' => __('message.deliverymandocument')]);
                }
                break;
            case 'document-checked':
                foreach ($checked_ids as $id) {
                    $document = Document::withTrashed()->where('id', $id)->first();
                    if ($document) {
                        if (env('APP_DEMO')) {
                            $message = __('message.demo_permission_denied');
                            if (request()->ajax()) {
                                return response()->json(['status' => false, 'message' => $message, 'event' => 'validation']);
                            }
                        }
                        if ($document->deleted_at != null) {
                            $document->forceDelete();
                        } else {
                            $document->delete();
                        }
                    }
                    $message = __('message.delete_form', ['form' => __('message.document')]);
                }
                break;
            case 'vehicle-checked':
                foreach ($checked_ids as $id) {
                    $vehicle = Vehicle::withTrashed()->where('id', $id)->first();
                    if ($vehicle) {
                        if (env('APP_DEMO')) {
                            $message = __('message.demo_permission_denied');
                            if (request()->ajax()) {
                                return response()->json(['status' => false, 'message' => $message, 'event' => 'validation']);
                            }
                        }
                        if ($vehicle->deleted_at != null) {
                            $vehicle->forceDelete();
                        } else {
                            $vehicle->delete();
                        }
                    }
                    $message = __('message.delete_form', ['form' => __('message.vehicle')]);
                }
                break;
            case 'language-list-checked':
                if (env('APP_DEMO')) {
                    $message = __('message.demo_permission_denied');
                    if (request()->ajax()) {
                        return response()->json(['status' => false, 'message' => $message, 'event' => 'validation']);
                    }
                }
                $data = LanguageList::destroy($checked_ids);
                $message = __('message.delete_form', ['form' => __('message.language')]);
                updateLanguageVersion();
                break;
            default:
                $message = false;
                break;
        }
        $response = [
            'success' => true,
            'message' => $message
        ];
        return json_custom_response($response);
    }

    public function saveWalletHistory(Request $request, $user_id)
    {
        $data = $request->all();
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user_id]
        );

        if ($data['type'] == 'credit') {
            $data['transaction_type'] = 'topup';
            $total_amount = $wallet->total_amount + $data['amount'];
        }

        if ($data['type'] == 'debit') {
            $data['transaction_type'] = 'correction';
            $total_amount = $wallet->total_amount - $data['amount'];
        }
        $currency_code = SettingData('CURRENCY', 'CURRENCY_CODE') ?? 'USD';
        $wallet->currency = strtolower($currency_code);

        $wallet->total_amount = $total_amount;
        $message = __('message.save_form', ['form' => __('message.wallet')]);
        try {
            DB::beginTransaction();
            $wallet->save();
            $data['user_id'] = $wallet->user_id;
            $data['balance'] = $total_amount;
            $data['datetime'] = date('Y-m-d H:i:s');
            $result = WalletHistory::create($data);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $e;
        }
        return redirect()->back()->withSuccess($message);
    }
    public function deliverymanlocation()
    {
        $pageTitle = __('message.delivery_boy_location');
        $assets = ['location'];

        $deliveryMen = User::where('user_type', 'delivery_man')
            ->where('status', '1')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->select('latitude', 'longitude', 'name', 'id', 'created_at', 'latitude', 'longitude')
            ->get();

        return view('deliveryman.map', compact('pageTitle', 'assets', 'deliveryMen'));
    }

    public function ordermaplocation(Request $request)
    {
        $pageTitle = __('message.orders_location');
        $assets = ['location'];
        $orders = Order::all();
        $pickupPoints = [];
        $deliveryPoints = [];
        $selectedStatus = 'all';
        if ($request->has('status_filter')) {
            $selectedStatus = $request->status_filter;
            $orders = Order::where('status', $selectedStatus)->get();
        }

        foreach ($orders as $order) {
            if (isset($order->pickup_point['latitude']) && isset($order->pickup_point['longitude'])) {
                $pickupPoints[] = [
                    'latitude' => $order->pickup_point['latitude'],
                    'longitude' => $order->pickup_point['longitude'],
                    'address' => $order->pickup_point['address'],
                    'order_id' => $order->id,
                    'created_at' => $order->created_at,
                    'status' => $order->status,
                ];
            }
            if (isset($order->delivery_point['latitude']) && isset($order->delivery_point['longitude'])) {
                $deliveryPoints[] = [
                    'latitude' => $order->delivery_point['latitude'],
                    'longitude' => $order->delivery_point['longitude'],
                    'address' => $order->delivery_point['address'],
                    'order_id' => $order->id,
                    'created_at' => $order->created_at,
                    'status' => $order->status,
                ];
            }
        }

        return view('order.map', compact('pageTitle', 'assets', 'pickupPoints', 'deliveryPoints', 'selectedStatus'));
    }
    public function bankDetails()
    {
        $client = Auth::user()->id;
        $pageTitle = __('message.bank_details');
        $userbankaccount = UserBankAccount::where('user_id', $client)->first();
        return view('clientside.bankadetails', compact('pageTitle', 'userbankaccount'));
    }
    public function bankDetailSave(Request $request)
    {
        $user = Auth::user()->id;
        $pageTitle = __('message.bank_details');
        $user_data = User::find($user);
        if ($user_data->userBankAccount != null && $request->has('user_bank_account')) {
            $user_data->userBankAccount->fill($request->user_bank_account)->update();
        } else if ($request->has('user_bank_account') && $request->user_bank_account != null) {
            $user_data->userBankAccount()->create($request->user_bank_account);
        }
        $message = __('message.update_form', ['form' => __('message.bank_details')]);
        if ($user_data->wasRecentlyCreated) {
            $message = __('message.save_form', ['name' => __('message.bank_details')]);
        }
        return redirect()->back()->withSuccess($message);
    }

    public function clientwallet()
    {
        if (Auth::user()->user_type == 'client') {
            $pageTitle = __('message.wallet');

            $client = Auth::user()->id;
            $wallet = WalletHistory::where('user_id', $client)->get();
            $withdrawn = Wallet::where('user_id', $client)->first();

            return view('clientside.wallet', compact('pageTitle', 'withdrawn', 'wallet'));
        }
    }

    public function changePasswordPage()
    {
        $pageTitle = __('message.chnage_password');

        return view('clientside.changepassword', compact('pageTitle'));
    }

    public function changePassword(Request $request)
    {
        $user = User::where('id', Auth::user()->id)->first();

        if ($user == "") {
            $message = __('message.user_not_found');
            redirect()->route('passwordpage.index')->withSuccess($message);
        }
        $hashedPassword = $user->password;

        $match = Hash::check($request->old_password, $hashedPassword);

        $same_exits = Hash::check($request->new_password, $hashedPassword);
        if ($match) {
            if ($same_exits) {
                $message = __('message.old_new_pass_same');
                return redirect()->route('passwordpage')->withSuccess($message);
            }

            $user->fill([
                'password' => Hash::make($request->new_password)
            ])->save();

            $message = __('message.password_change');
            return redirect()->route('passwordpage')->withSuccess($message);
        } else {
            $message = __('message.valid_password');
            return redirect()->route('passwordpage')->withErrors($message);
        }
        return redirect()->route('passwordpage')->withSuccess(__('message.password_succesfully_change'));
    }


    public function appsetting()
    {

        $pageTitle = __('message.account_setting');

        $client = Auth::user()->id;

        return view('clientside.appsetting', compact('client', 'pageTitle'));
    }
    public function dashboardfilter()
    {
        $pageTitle = __('message.filter');
        $params = null;

        $params = [
            'from_date' => request('from_date') ?? null,
            'to_date' => request('to_date') ?? null,
            'created_at' => request('created_at') ?? null,
        ];

        $selectedCityId = request('city_id');
        $cities = City::pluck('name', 'id')->prepend(__('message.select_name', ['select' => __('message.city')]), '')->toArray();
        $selectedCountryId = request('country_id');
        $country = Country::pluck('name', 'id')->prepend(__('message.select_name', ['select' => __('message.country')]), '')->toArray();

        return view('global.dashboardfilter-datatable', compact('pageTitle', 'params', 'selectedCityId', 'cities', 'selectedCountryId', 'country'));
    }
}
