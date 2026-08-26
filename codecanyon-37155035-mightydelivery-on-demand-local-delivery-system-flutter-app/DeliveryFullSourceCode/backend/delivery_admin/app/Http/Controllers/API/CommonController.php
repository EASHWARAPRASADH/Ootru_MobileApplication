<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use App\Models\Setting;
use App\Models\AppSetting;

class CommonController extends Controller
{
    // Old Cde

    // public function placeAutoComplete(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'search_text' => 'required',
    //         'country_code' => 'required',
    //         'language' => 'required'
    //     ]);

    //     if ( $validator->fails() ) {
    //         $data = [
    //             'status' => 'false',
    //             'message' => $validator->errors()->first(),
    //             'all_message' =>  $validator->errors()
    //         ];

    //         return json_custom_response($data,400);
    //     }

    //     $google_map_api_key = env('GOOGLE_MAP_API_KEY');
    //     // $response = Http::get('https://maps.googleapis.com/maps/api/place/autocomplete/json?input='.request('search_text').'&components=country:'.request('country_code').'&language:'.request('language').'&key='.$google_map_api_key);
    //     $response = Http::withHeaders([
    //         'Accept-Language' => request('language'),
    //     ])->get('https://maps.googleapis.com/maps/api/place/autocomplete/json?input='.request('search_text').'&components=country:'.request('country_code').'&key='.$google_map_api_key);
    //     return $response->json();
    // }

   
// Old code
    // public function placeDetail(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'placeid' => 'required',
    //     ]);

    //     if ( $validator->fails() ) {
    //         $data = [
    //             'status' => 'false',
    //             'message' => $validator->errors()->first(),
    //             'all_message' =>  $validator->errors()
    //         ];

    //         return json_custom_response($data,400);
    //     }

    //     $google_map_api_key = env('GOOGLE_MAP_API_KEY');
    //     $response = Http::get('https://maps.googleapis.com/maps/api/place/details/json?placeid='.$request->placeid.'&key='.$google_map_api_key);

    //     return $response->json();
    // }

    // Old code

     public function distanceMatrix(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'origins' => 'required',
            'destinations' => 'required',
        ]);

        if ( $validator->fails() ) {
            $data = [
                'status' => 'false',
                'message' => $validator->errors()->first(),
                'all_message' =>  $validator->errors()
            ];

            return json_custom_response($data,400);
        }

        $google_map_api_key = env('GOOGLE_MAP_API_KEY');
        $response = Http::get('https://maps.googleapis.com/maps/api/distancematrix/json?origins='.$request->origins.'&destinations='.$request->destinations.'&key='.$google_map_api_key.'&mode=driving');

        return $response->json();
    }

    public function placeAutoComplete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'search_text' => 'required',
            'language' => 'required'
        ]);
 
        if ( $validator->fails() ) {
            $data = [
                'status' => 'false',
                'message' => $validator->errors()->first(),
                'all_message' =>  $validator->errors()
            ];
 
            return json_custom_response($data,400);
        }
       
        $google_map_api_key = env('GOOGLE_MAP_API_KEY');
       
        $payload = ['input' => $request->input('search_text')];
 
        if ($request->has('language')) {
            $payload['languageCode'] = $request->input('language');
        }
 
        $response = Http::withHeaders([
            'X-Goog-Api-Key' => $google_map_api_key,
            'Content-Type' => 'application/json'
        ])->post('https://places.googleapis.com/v1/places:autocomplete', $payload);
        return $response->json();
    }

    public function placeDetail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'placeid' => 'required',
        ]);
 
        if ( $validator->fails() ) {
            $data = [
                'status' => 'false',
                'message' => $validator->errors()->first(),
                'all_message' =>  $validator->errors()
            ];
 
            return json_custom_response($data,400);
        }
       
        $google_map_api_key = env('GOOGLE_MAP_API_KEY');
        $placeId = $request->placeid;
        $apiUrl = "https://places.googleapis.com/v1/places/{$placeId}";
 
        $headers = [
            'Content-Type' => 'application/json',
            'X-Goog-Api-Key' => $google_map_api_key,
            'X-Goog-FieldMask' => 'id,displayName,formattedAddress,location'
        ];
 
        $response = Http::withHeaders($headers)->get($apiUrl);
        // $response = Http::get('https://maps.googleapis.com/maps/api/place/details/json?placeid='.$request->placeid.'&key='.$google_map_api_key);
 
        return $response->json();
    }

    // public function distanceMatrix(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'origins' => 'required',
    //         'destinations' => 'required',
    //     ]);
    
    //     if ($validator->fails()) {
    //         return json_custom_response([
    //             'status' => 'false',
    //             'message' => $validator->errors()->first(),
    //             'all_message' => $validator->errors()
    //         ], 400);
    //     }
    
    //     $google_map_api_key = env('GOOGLE_MAP_API_KEY');
    //     if (!$google_map_api_key) {
    //         return response()->json(['error' => 'Google Map API Key is missing'], 500);
    //     }
    
    //     list($originLat, $originLng) = explode(',', $request->origins);
    //     list($destinationLat, $destinationLng) = explode(',', $request->destinations);
    
    //     function getFormattedAddress($lat, $lng, $google_map_api_key) {
    //         $response = Http::get("https://maps.googleapis.com/maps/api/geocode/json", [
    //             'latlng' => "$lat,$lng",
    //             'key' => $google_map_api_key
    //         ]);
    
    //         if ($response->json()['status'] !== 'OK') {
    //             return "$lat,$lng"; // Fallback to lat,lng if no address found
    //         }
    
    //         return $response->json()['results'][0]['formatted_address'] ?? "$lat,$lng";
    //     }
    
    //     $originAddress = getFormattedAddress($originLat, $originLng, $google_map_api_key);
    //     $destinationAddress = getFormattedAddress($destinationLat, $destinationLng, $google_map_api_key);
    
    //     $response = Http::withHeaders([
    //         'Content-Type' => 'application/json',
    //         'X-Goog-Api-Key' => $google_map_api_key,
    //         'X-Goog-FieldMask' => 'originIndex,destinationIndex,distanceMeters,duration,status'
    //     ])->post('https://maps.googleapis.com/maps/api/distancematrix/json', [
    //         "origins" => ["$originLat,$originLng"],
    //         "destinations" => ["$destinationLat,$destinationLng"],
    //         "mode" => "driving"
    //     ]);
    
    //     if (!$response->successful()) {
    //         return response()->json(['error' => 'Failed to retrieve distance matrix'], 500);
    //     }
    
    //     $routesData = $response->json()['rows'][0]['elements'][0] ?? null;
    
    //     if (!$routesData) {
    //         return response()->json(['error' => 'No route data found'], 404);
    //     }
    
    //     $distanceMeters = $routesData['distance']['value'] ?? 0;
    //     $durationInSeconds = isset($routesData['duration']['value']) 
    //         ? (int)$routesData['duration']['value'] 
    //         : 0;
    
    //     $convertedResponse = [
    //         "rows" => [
    //             [
    //                 "elements" => [
    //                     [
    //                         "distance" => [
    //                             "text" => round($distanceMeters / 1000, 1) . " km",
    //                             "value" => $distanceMeters
    //                         ],
    //                         "duration" => [
    //                             "text" => round($durationInSeconds / 60) . " mins",
    //                             "value" => $durationInSeconds
    //                         ],
    //                         "status" => $routesData['status'] ?? "UNKNOWN"
    //                     ]
    //                 ]
    //             ]
    //         ],
    //         "status" => "OK"
    //     ];
    
    //     return response()->json($convertedResponse);
    //     if (!isset($routesData[0]['distanceMeters']) || !isset($routesData[0]['duration'])) {
    //         return response()->json([
    //             "status" => "ERROR",
    //             "message" => "Unable to fetch distance and duration. Check API response.",
    //             "api_response" => $routesData
    //         ], 400);
    //     }
    
    //     $convertedResponse = [
    //         "destination_addresses" => [$destinationAddress],
    //         "origin_addresses" => [$originAddress],
    //         "rows" => [
    //             [
    //                 "elements" => [
    //                     [
    //                         "distance" => [
    //                             "text" => round($routesData[0]['distanceMeters'] / 1000, 1) . " km",
    //                             "value" => $routesData[0]['distanceMeters']
    //                         ],
    //                         "duration" => [
    //                             "text" => round((int) rtrim($routesData[0]['duration'], "s") / 60) . " mins",
    //                             "value" => (int) rtrim($routesData[0]['duration'], "s")
    //                         ],
    //                         "status" => $routesData[0]['status'] ?? "UNKNOWN"
    //                     ]
    //                 ]
    //             ]
    //         ],
    //         "status" => "OK"
    //     ];
    //     return response()->json($convertedResponse);
    // }
} 
