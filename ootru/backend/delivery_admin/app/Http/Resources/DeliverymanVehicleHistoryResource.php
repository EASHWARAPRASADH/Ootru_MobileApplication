<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DeliverymanVehicleHistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'   => $this->id,
            'delivery_man_id' => $this->delivery_man_id,
            'delivery_man' => optional($this->delievryman)->name,
            'start_datetime' => $this->start_datetime,
            'end_datetime' => $this->end_datetime,
            'is_active' => $this->is_active,
            'vehicle_info' => json_decode($this->vehicle_info),
        ];
    }
}
