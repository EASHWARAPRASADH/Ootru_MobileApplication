<?php

namespace App\Imports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class ImportOrderdata implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if (empty(array_filter($row))) {
            return null;
        }
        $convertTime = function ($value) {
            if (is_numeric($value)) {
                $hours = (int) $value;
                $minutes = ($value - $hours) * 60;
                return sprintf('%02d:%02d', $hours, (int) $minutes);
            }
            return null;
        };
        $pickupPoint = [
            'start_time' => $convertTime($row['pickup_pointstart_time']) ?? null,
            'end_time' => $convertTime($row['pickup_pointend_time']) ?? null,
            'address' => $row['pickup_pointaddress'] ?? null,
            'latitude' => $row['pickup_pointlatitude'] ?? null,
            'longitude' => $row['pickup_pointlongitude'] ?? null,
            'contact_number' => $row['pickup_pointcontact_number'] ?? null,
            'description' => $row['pickup_pointdescription'] ?? null,
            'instruction' => $row['pickup_pointinstruction'] ?? null,
        ];
    
        $deliveryPoint = [
            'start_time' => $row['delivery_pointstart_time'] ?? null,
            'end_time' => $row['delivery_pointend_time'] ?? null,
            'address' => $row['delivery_pointaddress'] ?? null,
            'latitude' => $row['delivery_pointlatitude'] ?? null,
            'longitude' => $row['delivery_pointlongitude'] ?? null,
            'contact_number' => $row['delivery_pointcontact_number'] ?? null,
            'description' => $row['delivery_pointdescription'] ?? null,
            'instruction' => $row['delivery_pointinstruction'] ?? null,
        ];
        $order = new Order([
            'client_id' => auth()->user()->id,
            'parcel_type' => $row['parcel_type'] ?? null,
            'weight' => $row['weight'] ?? null,
            'number_of_parcel' => $row['number_of_parcel'] ?? null,
            'country_id' => $row['country_id'] ?? null,
            'city_id' => $row['city_id'] ?? null,
            'pickup_point' => $pickupPoint,
            'delivery_point' => $deliveryPoint,
            'vehicle_id' => $row['vehicle_id'] ?? null,
        ]);

        $order->save();
        return $order;
    }
}
