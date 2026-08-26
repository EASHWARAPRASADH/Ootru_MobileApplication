<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderPrintResource extends JsonResource
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
            'order_tracking_id'             => $this->milisecond,
            'client_id'                     => $this->client_id,
            'client_name'                   => optional($this->client)->name,
            'id'                            => $this->id,
            'pickup_point'                  => $this->pickup_point,
            'delivery_point'                => $this->delivery_point,
            'delivery_man_id'               => $this->delivery_man_id,
            'delivery_man_name'             => optional($this->delivery_man)->name,
            'pickup_datetime'               => $this->pickup_datetime,
            'delivery_datetime'             => $this->delivery_datetime,
            'created_at'                    => $this->created_at,
            'status'                        => $this->status,
        ];
    }
}
