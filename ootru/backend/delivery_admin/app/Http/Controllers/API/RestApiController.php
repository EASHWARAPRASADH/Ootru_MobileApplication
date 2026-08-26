<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\RestApiResource;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Models\City;
use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\RestApi;
use App\Models\RestApiHistory;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class RestApiController extends Controller
{
    public function getList(Request $request)
    {
        $parceltype = RestApi::MyRestApi();

        $per_page = config('constant.PER_PAGE_LIMIT');
        if( $request->has('per_page') && !empty($request->per_page)){
            if(is_numeric($request->per_page))
            {
                $per_page = $request->per_page;
            }
            if($request->per_page == -1 ){
                $per_page = $parceltype->count();
            }
        }

        $parceltype = $parceltype->orderBy('id', 'desc')->paginate($per_page);

        $items = RestApiResource::collection($parceltype);
        $status = 200;

        $response = [
            'status'        => $status,
            'pagination'    => json_pagination_response($items),
            'data'          => $items,
        ];

        return json_custom_response($response);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $restApi = RestApi::where('rest_key', $data['api_token'])->first();
    
        if (!$restApi) {
            return response()->json([
                'message' => __('message.invalid_token'),
                'success' => false
            ], 400);
        }
        $validator = Validator::make($request->all(), [
            'date' => 'required|date|date_format:Y-m-d',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => __('message.date_format_not_valid'),
                'success' => false,
                // 'errors' => $validator->errors(),
            ], 400);
        }
        $clientEmail = $data['customer']['email_id'] ?? null;
        $pickupPoint = $data['pickup_point']['address'] ?? null;
        $pickupLat = $data['pickup_point']['latitude'] ?? null;
        $pickupLng = $data['pickup_point']['longitude'] ?? null;

        $deliveryPoint = $data['delivery_point']['address'] ?? null;
        $deliveryLat = $data['delivery_point']['latitude'] ?? null;
        $deliveryLng = $data['delivery_point']['longitude'] ?? null;

        if (is_null($pickupPoint) || is_null($pickupLat) || is_null($pickupLng)) {
        return response()->json([
            'message' => is_null($pickupPoint) ? __('message.please_fill_valid_pickup_point') : __('message.please_fill_valid_pickup_point'),
            'success' => false
        ], 400);
        }

        if (is_null($deliveryPoint) || is_null($deliveryLat) || is_null($deliveryLng)) {
        return response()->json([
            'message' => is_null($deliveryPoint) ? __('message.please_fill_valid_delivery_point') : __('message.please_fill_valid_delivery_point'),
            'success' => false
        ], 400);
        }


        if (is_null($clientEmail)) {
            return response()->json([
                'message' => __('message.email_required'),
                'success' => false
            ], 400);
        }
        if (!filter_var($clientEmail, FILTER_VALIDATE_EMAIL)) {
            return response()->json([
                'message' => __('message.invalid_email'),
                'success' => false
            ], 400);
        }

        $mobileNumber = $data['customer']['mobile_number'];
        if (!is_numeric($mobileNumber) || strlen($mobileNumber) < 10 || strlen($mobileNumber) > 15) {
            return response()->json([
                'message' => __('message.validation_error'),
                'success' => false
            ], 400);
        }
        foreach ($data['extra_charges'] as $charge) {
            if (!is_numeric($charge['value']) || $charge['value'] <= 0) {
                return response()->json([
                    'message' => __('message.invalid_value'),
                    'success' => false
                ], 400);
            }
        }
        $packing_symbols = [
            'this_way_up',
            'do_not_stack',
            'temperature_sensitive',
            'do_not_use_hooks',
            'explosive_material',
            'hazardous_material',
            'bike_delivery',
            'keep_dry',
            'perishable',
            'recycle',
            'do_not_open_with_sharp_objects',
            'fragile'
        ];
        $requested_symbols = $data['packaging_symbols'] ?? [];
        $requested_keys = array_column($requested_symbols, 'key');
        $invalid_keys = array_diff($requested_keys, $packing_symbols);

        if (!empty($invalid_keys)) {
            return response()->json([
                'message' => __('message.packaging_symbols_invalid'),
                'success' => false,
                'invalid_keys' => $invalid_keys
            ], 400);
        }

        if ($data['payment_collect_from'] !== 'on_pickup' && $data['payment_collect_from'] !== 'on_delivery') {
            return response()->json([
                'message' => __('message.invalid_collection'),
                'success' => false
            ], 400);
        }
        if ($data['payment_type'] !== 'online' && $data['payment_type'] !== 'cash') {
            return response()->json([
                'message' => __('message.invalid_payment_type'),
                'success' => false
            ], 400);
        }

        if (
            !is_numeric($data['total_weight']) || $data['total_weight'] <= 0 ||
            !is_numeric($data['parcel_number']) || $data['parcel_number'] <= 0
        ) {
            return response()->json([
                'message' => __('message.invalid_value'),
                'success' => false
            ], 400);
        }
        $client = User::where('email', $clientEmail)->first();
        if ($client) {
            $client_id = $client->id;
        } else {
            $newClient = User::create([
                'email' => $clientEmail,
                'name' => $data['customer']['name'] ?? 'Test',
                'username' => $data['customer']['name'] ?? 'Test',
                'contact_number' => $data['customer']['mobile_number'],
                'user_type' => 'client',
                'password' => bcrypt('12345678')
            ]);
            $client_id = $newClient->id;
        }

        $data['client_id'] = $client_id;

        $symbols = $request->input('packaging_symbols');

        if (is_array($symbols)) {
            $formattedSymbols = array_map(function ($symbol) {
                return isset($symbol['title'], $symbol['key']) ? [
                    'title' => $symbol['title'],
                    'key' => $symbol['key'],
                ] : null;
            }, $symbols);

            $formattedSymbols = array_filter($formattedSymbols);
            $data['packaging_symbols'] = json_encode($formattedSymbols);
        }

        if (!$request->is('api/*')) {
            $extraCharges = $request->input('extra_charges');

            if ($extraCharges) {
                $extraCharges = json_decode($extraCharges, true);
                if (is_array($extraCharges)) {
                    $formattedCharges = array_map(function ($charge) {
                        return isset($charge['title'], $charge['charges'], $charge['charges_type']) ? [
                            'key' => $charge['title'],
                            'value' => $charge['charges'],
                            'value_type' => $charge['charges_type']
                        ] : null;
                    }, $extraCharges);

                    $formattedCharges = array_filter($formattedCharges);
                    $data['extra_charges'] = $formattedCharges;
                }
            }
        }
        $data['milisecond'] = strtoupper(appSettingcurrency('prefix')) . '' . round(microtime(true) * 1000);
        $data['country_id'] = $restApi->country_id;
        $data['city_id'] = $restApi->city_id;
        $data['status'] = 'create';
        $data['payment_type'] = $request->payment_type;

        $restApi = RestApi::where('rest_key', $data['api_token'])->first();
        if (!$restApi) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid API token'
            ], 401);
        }

        $city = City::where('id', $restApi->city_id)->first();

        if (!$city) {
            return response()->json([
                'success' => false,
                'message' => 'City not found for the provided API token'
            ], 404);
        }
        $fixedCharges = $city->fixed_charges;
        $calculation = $this->calculateTotalAmount($data, $city);

        $totalAmount = $calculation['total_amount'] + $fixedCharges;
        if (!isset($calculation['distance_charge'], $calculation['weight_charge'])) {
            return response()->json([
                'success' => false,
                'message' => 'Error calculating charges'
            ], 500);
        }

        if (!empty($data['extra_charge'])) {
            $totalAmount += $data['extra_charge'];
        }

        $data['distance_charge'] = $calculation['distance_charge'];
        $data['weight_charge'] = $calculation['weight_charge'];
        $data['fixed_charges'] = $fixedCharges;
        $data['total_amount'] = round($totalAmount, 2);

        $currency = appSettingcurrency()->currency ?? '$';
        $data['currency'] = $currency;

        $restApiHistoryCount = RestApiHistory::where('rest_key', $data['api_token'])->count();
        if (env('APP_DEMO')) {
            if ($restApiHistoryCount >= 2) {
                return response()->json([
                    'message' => 'No more orders can be created with this API token in demo mode.'
                ], 403);
            }
        }
        $result = Order::Create($data);

        if ($result) {
            $restApi->last_access_date = now();
            $restApi->save();
            
            $dataOrderHistor['history_type'] = $result->status;
            $dataOrderHistor['history_message'] = __('message.order_create');
            $history_data = [
                'client_id' => $result->client_id,
                'client_name' => isset($result->client) ? $result->client->name : '',
            ];
            $dataOrderHistor['history_data'] = json_encode($history_data);
            $dataOrderHistor['order_id'] = $result->id;
            OrderHistory::create($dataOrderHistor);
        }
        $restApiHistory = RestApiHistory::create([
            'rest_key' => $data['api_token'],
            'order_id' => $result->id,
            'last_access_date' => $restApi->last_access_date
        ]);
        $message = __('message.save_form', ['form' => __('message.order')]);
        if ($request->is('api/*')) {
            $response = [
                'order_id' => $result->id,
                'message' => $message,
                'status' => true
            ];
            return json_custom_response($response);
        }
    }

    private function calculateTotalAmount($data, $city)
    {
        $origins = "{$data['pickup_point']['latitude']},{$data['pickup_point']['longitude']}";
        $destinations = "{$data['delivery_point']['latitude']},{$data['delivery_point']['longitude']}";

        $distanceResponse = $this->distanceMatrix(new Request([
            'origins' => $origins,
            'destinations' => $destinations,
        ]));

        if (!isset($distanceResponse['rows'][0]['elements'][0]['distance']['value'])) {
            return [
                'success' => false,
                'message' => 'Unable to calculate distance. Please try again later.',
            ];
        }
        $distance = $distanceResponse['rows'][0]['elements'][0]['distance']['value'] / 1000;
        $effectiveDistance = max(0, $distance - $city->min_distance);
        $distanceCharge = $effectiveDistance * $city->per_distance_charges;

        $effectiveWeight = isset($data['total_weight']) ? max(0, $data['total_weight'] - $city->min_weight) : 0;
        $weightCharge = $effectiveWeight * $city->per_weight_charges;
        $totalAmount = $distanceCharge + $weightCharge;

        return [
            'distance' => $distance,
            'distance_charge' => $distanceCharge,
            'weight_charge' => $weightCharge,
            'total_amount' => $totalAmount,
        ];
    }
    
    public function getCount(Request $request)
    {
        $data = $request->all();
        $restApi = RestApi::where('rest_key', $data['api_token'])->first();
        if (!$restApi) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid API token',
            ], 401);
        }

        $city = City::where('id', $restApi->city_id)->first();
        if (!$city) {
            return response()->json([
                'success' => false,
                'message' => 'City not found for the provided API token',
            ], 404);
        }

        $origins = "{$data['pickup_point']['latitude']},{$data['pickup_point']['longitude']}";
        $destinations = "{$data['delivery_point']['latitude']},{$data['delivery_point']['longitude']}";


        $distanceResponse = $this->distanceMatrix(new Request([
            'origins' => $origins,
            'destinations' => $destinations,
        ]));

        if (!isset($distanceResponse['rows'][0]['elements'][0]['distance']['value'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to calculate distance. Please try again later.',
            ], 500);
        }


        $distance = $distanceResponse['rows'][0]['elements'][0]['distance']['value'] / 1000;

        $effectiveDistance = max(0, $distance - $city->min_distance);
        $totalDistanceCharges = $effectiveDistance * $city->per_distance_charges;

        $effectiveWeight = max(0, $data['total_weight'] - $city->min_weight);
        $totalWeightCharges = $effectiveWeight * $city->per_weight_charges;

        $totalAmount = $totalDistanceCharges + $totalWeightCharges + $city->fixed_charges;

        if (isset($data['extra_charges']) && is_array($data['extra_charges'])) {
            foreach ($data['extra_charges'] as $charge) {
                if ($charge['value_type'] == 'percentage') { 
                    $totalAmount += $totalAmount * ($charge['value'] / 100);
                } elseif ($charge['value_type'] == 'fixed') {
                    $totalAmount += $charge['value'];
                }
            }
        
        }
        return response()->json([
            'success' => true,
            'distance' => round($distance, 2), 
            'total_weight' => $data['total_weight'],
            'total_parcel_number' => $data['total_parcel'],
            'delivery_charge' => $city->fixed_charges,
            'extra_charges' => isset($data['extra_charges']) ? $data['extra_charges'] : [],
            'total_amount' => round($totalAmount, 2),
        ]);
    }
    public function distanceMatrix(Request $request)
    {
        $google_map_api_key = env('GOOGLE_MAP_API_KEY');
        $response = Http::get('https://maps.googleapis.com/maps/api/distancematrix/json', [
            'origins' => $request->origins,
            'destinations' => $request->destinations,
            'key' => $google_map_api_key,
            'mode' => 'driving',
        ]);
    
        return $response->json();
    }  
}
