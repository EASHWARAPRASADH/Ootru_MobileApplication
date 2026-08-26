<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CityResource;
use App\Http\Resources\CountryResource;
use App\Http\Resources\DeliveryManEarningResource;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\FrontendData;
use App\Models\AppSetting;
use App\Http\Resources\FrontendDataResource;
use App\Http\Resources\OrderResource;
use App\Http\Resources\SettingResource;
use App\Http\Resources\StaticDataResource;
use App\Http\Resources\UserAddressResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\VehicleResource;
use App\Models\City;
use App\Models\Country;
use App\Models\Emergency;
use App\Models\Order;
use App\Models\Payment;
use App\Models\StaticData;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\Vehicle;
use App\Models\Wallet;
use App\Models\WithdrawRequest;

class DashboardController extends Controller
{
    public function appsetting(Request $request)
    {
        $data['app_setting'] = AppSetting::first();

        $data['terms_condition'] = Setting::where('type','terms_condition')->where('key','terms_condition')->first();
        $data['privacy_policy'] = Setting::where('type','privacy_policy')->where('key','privacy_policy')->first();

        $currency_code = SettingData('CURRENCY', 'CURRENCY_CODE') ?? 'USD';
        $currency = currencyArray($currency_code);

        $data['currency_setting'] = [
            'name' => $currency['name'] ?? 'United States (US) dollar',
            'symbol' => $currency['symbol'] ?? '$',
            'code' => strtolower($currency['code']) ?? 'usd',
            'position' => SettingData('CURRENCY', 'CURRENCY_POSITION') ?? 'left',
        ];
        return json_custom_response($data);
    }


    public function commonDashboard($request)
    {
        $data['app_seeting'] = AppSetting::first();

        $data['terms_condition'] = Setting::where('type','terms_condition')->where('key','terms_condition')->first();
        $data['privacy_policy'] = Setting::where('type','privacy_policy')->where('key','privacy_policy')->first();

        $currency_code = SettingData('CURRENCY', 'CURRENCY_CODE') ?? 'USD';
        $currency = currencyArray($currency_code);

        $data['currency_setting'] = [
            'name' => $currency['name'] ?? 'United States (US) dollar',
            'symbol' => $currency['symbol'] ?? '$',
            'code' => strtolower($currency['code']) ?? 'usd',
            'position' => SettingData('CURRENCY', 'CURRENCY_POSITION') ?? 'left',
        ];
        return $data;
    }

    public function websiteData()
    {
        $appsetting = AppSetting::first();
        $response = [
            'play_store_link'   => SettingData('app_content', 'play_store_link') ?? null,
            'app_store_link'    => SettingData('app_content', 'app_store_link') ?? null,
            'company_name'      => SettingData('app_content', 'company_name') ?? null,
            'app_name'          => SettingData('app_content', 'app_name') ?? null,
            'download_text'     => SettingData('app_content', 'download_text') ?? null,
            'purchase_url'      => SettingData('app_content', 'purchase_url') ?? null,
            'download_footer_content' => SettingData('app_content', 'download_footer_content') ?? null,
            'app_ss_image' => SettingData('app_content', 'app_ss_image') ?? null,
            'app_logo_image' => SettingData('app_content', 'app_logo_image') ?? null,
            'delivery_man_image' => SettingData('app_content', 'delivery_man_image') ?? null,
            'privacy_policy' => SettingData('privacy_policy', 'privacy_policy') ?? null,
            'term_and_condition' => SettingData('terms_condition', 'terms_condition') ?? null,
        ];

        $response['appsetting'] = $appsetting;
        $response['about_us'] = [
            'sort_des'      => SettingData('about_us', 'sort_des') ?? null,
            'long_des'      => SettingData('about_us', 'long_des') ?? null,
            'download_title'=> SettingData('about_us', 'download_title') ?? null,
            'download_subtitle' => SettingData('about_us', 'download_subtitle') ?? null,
            'about_us_app_ss'   => SettingData('about_us', 'about_us_app_ss') ?? null,
        ];

        $response['download_app'] = [
            'download_title'     => SettingData('download_app', 'download_title') ?? null,
            'download_description'  => SettingData('download_app', 'download_description') ?? null,
            'download_app_logo' => SettingData('download_app', 'download_app_logo') ?? null,
        ];

        $response['contact_us'] = [
            'contact_title'     => SettingData('contact_us', 'contact_title') ?? null,
            'contact_subtitle'  => SettingData('contact_us', 'contact_subtitle') ?? null,
            'contact_us_app_ss' => SettingData('contact_us', 'contact_us_app_ss') ?? null,
        ];

        $partner_benefits = FrontendData::where('type', 'partner_benefits')->get();
        $response['delivery_partner'] = [
            'title' => SettingData('delivery_partner', 'title') ?? null,
            'subtitle' => SettingData('delivery_partner', 'subtitle') ?? null,
            'image'    => SettingData('delivery_partner', 'delivery_partner_image') ?? null,
            'benefits' => FrontendDataResource::collection($partner_benefits)
        ];

        $why_choose = FrontendData::where('type', 'why_choose')->get();
        $response['why_choose'] = [
            'title'  => SettingData('why_choose', 'title') ?? null,
            'description'  => SettingData('why_choose', 'description') ?? null,
            'data' => FrontendDataResource::collection($why_choose)
        ];

        $client_review = FrontendData::where('type', 'client_review')->get();
        $response['client_review'] = [
            'client_review_title'  => SettingData('client_review', 'client_review_title') ?? null,
            'data' => FrontendDataResource::collection($client_review),
        ];

        $walkthrough =  FrontendData::where('type', 'walkthrough')->get();
        $response['walkthrough'] = FrontendDataResource::collection($walkthrough);

        $response['track_order'] = [
            'track_order_title'     => SettingData('track_order', 'track_order_title') ?? null,
            'track_order_subtitle'  => SettingData('track_order', 'track_order_subtitle') ?? null,
            'track_page_title' => SettingData('track_order', 'track_page_title') ?? null,
            'track_page_description' => SettingData('track_order', 'track_page_description') ?? null,
        ];

        return json_custom_response($response);
    }


    public function getdashboardlist(Request $request){
        $auth = Auth()->user();

            $user = User::where('id',$auth->id);
            $order = Order::where('client_id',$auth)->myOrder();
            $country = Country::query();
            $city = City::query();
            $setting = Setting::query();

            if ($request->has('id') && isset($request->id)) {
                $appsetting = AppSetting::where('id', $request->id)->first();
            } else {
                $appsetting = AppSetting::first();
            }

            if ($request->has('status') && isset($request->status)) {
                if (request('status') == 'trashed') {
                    $order = $order->withTrashed();
                } else {
                    $order = $order->where('status', request('status'));
                }
            };

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

            $order->when(request('statuses'), function ($q) {
                $statuses = explode(',', request('statuses'));
                return $q->whereIn('status', $statuses);
            });

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
        $user = $user->orderBy('id', 'desc')->paginate($per_page);
        $items = UserResource::collection($user);

        $setting = $setting->orderBy('key', 'asc')->paginate($request->per_page);

        $setting = SettingResource::collection($setting);


        $order = $order->orderBy('id', 'desc')->paginate($per_page);
        $order_list = OrderResource::collection($order);

        $cities = City::with('country')->where('status', 1)->get()->groupBy(function ($city) {
            return $city->country->name;
        });

        $groupedCitiesArray = $cities->map(function ($cities, $key) {
            $countryData = (new CountryResource($cities->first()->country))->toArray(request());
            $cityData = $cities->map(function ($city) {
                $cityArray = $city->toArray();
                unset($cityArray['country']);
                return $cityArray;
            });

            return array_merge($countryData, ['cities' => $cityData]);
        })->values()->toArray();

        $all_unread_count = isset($auth->unreadNotifications) ? $auth->unreadNotifications->count() : 0;

        $wallet_data = Wallet::where('user_id', auth()->id())->first();
        $userObject = (object) $items[0];
        $response = [
            'pagination' => json_pagination_response($order),
            'user' => $userObject,
            'order' => $order_list,
            'country' => $groupedCitiesArray,
            'app_setting' => $appsetting,
            'setting' => $setting,
            'all_unread_count' => $all_unread_count,
            'wallet_data' => $wallet_data ?? null,
        ];

        return json_custom_response($response);
    }

    public function deliverymanDashboard(Request $request)
    {
        $auth_user = auth()->user();
        if ($auth_user && $auth_user->user_type === 'delivery_man') {
            $data = [];
            $todayOrder = Order::where('delivery_man_id', $auth_user->id);
            $data['today_order'] = $todayOrder->whereDate('created_at', today())->count();

             $pendingOrder =  Order::where('delivery_man_id', $auth_user->id)->where('city_id', $auth_user->city_id)->whereDate('created_at', today());
            if ($request->has('from_date') && $request->has('to_date')) {
                $pendingOrder->whereBetween('created_at', [$request->input('from_date'), $request->input('to_date')]);
            }
            $data['pending_order'] = $pendingOrder->where('status', 'courier_assigned')->count();

            $inprogressOrder = Order::where('delivery_man_id', $auth_user->id)->where('city_id', $auth_user->city_id);
            if ($request->has('from_date') && $request->has('to_date')) {
                $inprogressOrder->whereBetween('created_at', [$request->input('from_date'), $request->input('to_date')]);
            }
            $data['inprogress_order'] = $inprogressOrder->whereIn('status', ['courier_departed', 'courier_arrived', 'courier_picked_up', 'active'])->count();

            $completeOrder = Order::where('delivery_man_id', $auth_user->id)->where('city_id', $auth_user->city_id);
            if ($request->has('from_date') && $request->has('to_date')) {
                $completeOrder->whereBetween('created_at', [$request->input('from_date'), $request->input('to_date')]);
            }
            $data['complete_order'] = $completeOrder->where('status', 'completed')->count();

            $earning_detail = User::withTrashed()
            ->where('id', $auth_user->id)
            ->withSum('getPayment as delivery_man_commission', 'delivery_man_commission')
            ->first();
            if ($request->has('from_date') && $request->has('to_date')) {
                    $earning_detail->whereBetween('created_at', [$request->input('from_date'), $request->input('to_date')]);
                }
                $data['commission']  = $earning_detail->delivery_man_commission ?? 0;

            $wallet_balance =Wallet::where('user_id', $auth_user->id)->first();
            if ($request->has('from_date') && $request->has('to_date')) {
                if($wallet_balance != null){
                    $wallet_balance->whereBetween('created_at', [$request->input('from_date'), $request->input('to_date')]);
                }else{
                    $wallet_balance = null;
                }
            }

            $data['wallet_balance'] = $wallet_balance ? $wallet_balance->total_amount ?? 0 : 0;


            $wallet_balance =WithdrawRequest::where('user_id', $auth_user->id);
            if ($request->has('from_date') && $request->has('to_date')) {
                $wallet_balance->whereBetween('created_at', [$request->input('from_date'), $request->input('to_date')]);
            }
            $data['pending_withdraw_request'] =$wallet_balance->where('status', 'requested')->count();

            $wallet_balance =WithdrawRequest::where('user_id', $auth_user->id);
            if ($request->has('from_date') && $request->has('to_date')) {
                $wallet_balance->whereBetween('created_at', [$request->input('from_date'), $request->input('to_date')]);
            }
            $data['complete_withdraw_request'] =$wallet_balance->where('status', 'approved')->count();
            $data['deliveryman_fees_at'] = $earning_detail->deliveryman_fees_at;

            $emergencyCount = Emergency::where('delivery_man_id', $auth_user->id)->where('status', 0)->count();
            $data['is_emergency'] = $emergencyCount > 0 ? true : false;        
        }
        return json_custom_response($data);
    }

    public function multipleDetails(Request $request)
    {
        $city_detail = null;
        if($request->city_id){
            $id = $request->city_id;

            $city = City::where('id',$id)->first();
            if(empty($city))
            {
                $message = __('message.not_found_entry',['name' =>__('message.city')]);
                return json_message_response($message,400);
            }

            $city_detail = new CityResource($city);
        }

        // vehicle list
        $vehicle = Vehicle::query()->whereStatus(1)
            ->when(request('city_id'), function ($q) {
                $city_id = request('city_id');
                return $q->whereJsonContains('city_ids', $city_id)
                        ->orWhere('type', 'all');
            })
            ->orderBy('title', 'asc')
            ->get();

        $vehicle_detail = VehicleResource::collection($vehicle);

        //User Address List
        $useraddress = UserAddress::myAddress()->get();
        $userAddress_details = UserAddressResource::collection($useraddress);

        // static List
        $staticData = StaticData::query()->get();
        $static_detail = StaticDataResource::collection($staticData);

        //app Setting List

        $appSettingcurrency = appSettingcurrency();
        $appsetting_details =[
            'currency_code' => $appSettingcurrency->currency_code,
            'currency' => $appSettingcurrency->currency,
            'currency_position' => $appSettingcurrency->currency_position,
            'is_vehicle_in_order' => $appSettingcurrency->is_vehicle_in_order,
            'is_bidding_in_order' =>$appSettingcurrency->is_bidding_in_order,
            'is_sms_order' =>$appSettingcurrency->is_sms_order,
            'insurance_allow' => SettingData('insurance_allow', 'insurance_allow'),
            'insurance_perntage' => SettingData('insurance_percentage', 'insurance_percentage'),
            'insurance_description' => SettingData('insurance_description', 'insurance_description'),
        ];

        $response = [
            'city-detail' => $city_detail,
            'vehicle-detail' => $vehicle_detail,
            'useraddress-detail' => $userAddress_details,
            'static-details' =>  $static_detail,
            'app-setting-detail' => $appsetting_details,
        ];
        return json_custom_response($response);
    }

}
