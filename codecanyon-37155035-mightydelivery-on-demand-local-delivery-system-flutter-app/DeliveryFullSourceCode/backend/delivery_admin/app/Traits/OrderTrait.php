<?php

namespace App\Traits;

use App\Models\User;
use App\Models\AppSetting;
use App\Models\Document;
use App\Models\OrderBid;
use App\Notifications\CommonNotification;
use Twilio\Rest\Client;

trait OrderTrait
{

    public function autoAssignOrder($order_data, $request_data = null)
    {
        $bidType = $order_data->bid_type;
        if ($bidType == 0) {
            $requiredDocumentIds = Document::where('is_required', 1)
                ->where('status', 1)
                ->pluck('id')
                ->toArray();

            $latitude = $order_data->pickup_point['latitude'];
            $longitude = $order_data->pickup_point['longitude'];
            $app_setting = AppSetting::first();
            $unit = isset($app_setting->distance_unit) ? $app_setting->distance_unit : 'km';
            $radius = isset($app_setting->distance) ? $app_setting->distance : 50;
            $unit_value = convertUnitvalue($unit);

            $nearby_deliveryperson = User::selectRaw("id, user_type, vehicle_id, latitude, longitude, ( $unit_value * acos( cos( radians($latitude) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians($longitude) ) + sin( radians($latitude) ) * sin( radians( latitude ) ) ) ) AS distance")
                ->where('city_id', $order_data->city_id)
                ->where('status', 1)
                ->where('user_type', 'delivery_man')
                ->where('vehicle_id', $order_data->vehicle_id)
                ->having('distance', '<=', $radius)
                ->orderBy('distance', 'asc')
                ->where(function ($query) {
                    $query->whereNotNull('email_verified_at')
                        ->whereNotNull('otp_verify_at')
                        ->whereNotNull('document_verified_at');
                });

            $nearby_deliveryperson = $nearby_deliveryperson->when(request('cancelled_delivery_man_ids'), function ($q) {
                return $q->whereNotIn('id', request('cancelled_delivery_man_ids'));
            })->first();

            if (request('cancelled_delivery_man_ids') != null) {
                $history_data = [
                    'history_type' => 'courier_auto_assign_cancelled',
                    'order_id' => $order_data->id,
                    'order' => $order_data,
                ];
                saveOrderHistory($history_data);
            }

            if ($nearby_deliveryperson != null) {
                $data = [
                    'auto_assign' => 1,
                    'cancelled_delivery_man_ids' => array_key_exists('cancelled_delivery_man_ids', $request_data) ? $request_data['cancelled_delivery_man_ids'] : null,
                    'delivery_man_id' => $nearby_deliveryperson->id,
                    'status' => 'courier_assigned',
                ];
                $order_data->fill($data)->update();

                $history_data = [
                    'history_type' => 'courier_assigned',
                    'order_id' => $order_data->id,
                    'order' => $order_data,
                ];
                saveOrderHistory($history_data);
            } else {
                $data = [
                    'status' => 'create',
                    'auto_assign' => 0,
                    'cancelled_delivery_man_ids' => array_key_exists('cancelled_delivery_man_ids', $request_data) ? $request_data['cancelled_delivery_man_ids'] : null,
                    'delivery_man_id' => null,
                ];
                $order_data->fill($data)->update();
            }
        }
        return $order_data;
    }


    public function sendTwilioSMS($order)
    {
        $is_twilio_sms = (bool) SettingData('twilio', 'is_twilio_sms') ?? false;

        $sid    = env('TWILIO_ACCOUNT_SID');
        $token  = env('TWILIO_AUTH_TOKEN');
        $sms_from = env('TWILIO_FROM');
        $whatsapp_from = env('TWILIO_WHATSAPP_FROM');

        if (in_array(null, [$sid, $token])) {
            return;
        }

        $twilio = new Client($sid, $token);
        $pickup_number = $order->pickup_point['contact_number'];

        $message = __('message.twilio_order.client_create', ['id' => $order->id]);
        if ($is_twilio_sms && $sms_from != null) {
            $twilio->messages->create(
                $pickup_number,
                array(
                    'from' => $sms_from,
                    'body' => $message
                )
            );
        }

        $is_twilio_whatsapp = (bool) SettingData('twilio', 'is_twilio_whatsapp') ?? false;

        if ($is_twilio_whatsapp && $whatsapp_from != null) {
            $twilio->messages->create(
                'whatsapp:' . $pickup_number,
                [
                    'from' => 'whatsapp:' . $whatsapp_from,
                    'body' => $message,
                ]
            );
        }
    }
    public function nearByDeliveryman($result, $request_data = null)
    {
        $latitude = $result->pickup_point['latitude'];
        $longitude = $result->pickup_point['longitude'];

        $app_setting = AppSetting::first();
        $unit = isset($app_setting->distance_unit) ? $app_setting->distance_unit : 'km';
        $radius = isset($app_setting->distance) ? $app_setting->distance : 50;
        $unit_value = convertUnitvalue($unit);

        $nearby_deliveryperson = User::selectRaw("id, user_type, latitude, longitude, player_id,( $unit_value * acos( cos( radians($latitude) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians($longitude) ) + sin( radians($latitude) ) * sin( radians( latitude ) ) ) ) AS distance")
            ->where('city_id', $result->city_id)
            ->where('status', 1)
            ->where('user_type', 'delivery_man')
            ->having('distance', '<=', $radius)
            ->orderBy('distance', 'asc')
            ->where(function ($query) {
                $query->whereNotNull('email_verified_at')
                    ->whereNotNull('otp_verify_at')
                    ->whereNotNull('document_verified_at');
            });


        $nearby_deliveryperson = $nearby_deliveryperson->when(request('cancelled_delivery_man_ids'), function ($q) {
            return $q->whereNotIn('id', request('cancelled_delivery_man_ids'));
        })->get();

        if ($nearby_deliveryperson->isEmpty()) {
            $this->updateFirebaseOrderData($result, []);
        }
        $delivery_man_ids = [];
        foreach ($nearby_deliveryperson as $nearby_delivery_man) {
            if ($nearby_delivery_man->player_id) {
                $notification_data = [
                    'id' => '',
                    'type' => 'new_order_requested',
                    'subject' =>  __('message.new_order_requested'),
                    'message' => __('message.new_order_requested'),
                ];
                $nearby_delivery_man->notify(new CommonNotification($notification_data['type'], $notification_data));
            }

            $delivery_man_ids[] = $nearby_delivery_man->id;
            $result->nearby_driver_ids = json_encode($delivery_man_ids);
            $result->save();

            OrderBid::create([
                'order_id' => $result->id,
                'is_bid_accept' => null, 
                'delivery_man_id' => $nearby_delivery_man->id,
                'bid_amount' => null,
                'notes' => null,
            ]);

            $this->updateFirebaseOrderData($result, $delivery_man_ids);
        }
    }

    private function updateFirebaseOrderData($result, $delivery_man_ids)
    {
        $document_name = 'order_' . $result->id;
        $firebaseData = app('firebase.firestore')->database()->collection('delivery_man')->document($document_name);

        if ($firebaseData) {
            $orderData = [
                'all_delivery_man_ids' => $delivery_man_ids ?? [],
                'order_id' => $result->id ?? '',
                'client_id' => $result->client_id ?? '',
                'client_name' => $result->client->name,
                'client_email' => $result->client->email,
                'status' => $result->status ?? '',
                'payment_status' => '',
                'payment_type' => '',
                'delivery_man_listening' => 0,
                'client_image' => getSingleMedia($result->client, 'profile_image', null),
                'order_has_bids' => $result->bid_type == 1 ? 1 : 0,
                'created_at' => $result->created_at,
            ];

            $firebaseData->set($orderData);
        }
    }

    // public function playAlertSound()
    // {
    //     $os = PHP_OS_FAMILY;
    //     $soundFile = public_path("alert.mp3"); 

    //     if (!file_exists($soundFile)) {
    //         echo "Sound file not found at: $soundFile";
    //         return;
    //     }

    //     if ($os === 'Windows') {
    //         $wavFile = str_replace('.mp3', '.wav', $soundFile);

    //         if (file_exists($wavFile)) {
    //             exec("powershell -c (New-Object Media.SoundPlayer '$wavFile').PlaySync();");
    //         } else {
               
    //             exec("start wmplayer \"$soundFile\"");
    //             sleep(5); 
    //             exec("taskkill /IM wmplayer.exe /F");
    //         }

    //     } elseif ($os === 'Darwin') {
    //         exec("afplay '$soundFile' > /dev/null 2>&1");

    //     } elseif ($os === 'Linux') {
    //         $escapedPath = escapeshellarg($soundFile); 
        
    //         if (shell_exec("command -v paplay")) {
    //             exec("paplay $escapedPath > /dev/null 2>&1");
    //         } elseif (shell_exec("command -v aplay")) {
    //             exec("aplay $escapedPath > /dev/null 2>&1");
    //         } elseif (shell_exec("command -v mpg123")) {
    //             exec("mpg123 $escapedPath > /dev/null 2>&1");
    //         } elseif (shell_exec("command -v ffplay")) {
    //             exec("ffplay -nodisp -autoexit -t 5 $escapedPath > /dev/null 2>&1");
    //         } else {
    //             echo "\007"; 
    //         }
    //     }
    // }

}
