<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderReportResource extends JsonResource
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
            'tracking_id'                =>$this->milisecond,
            'order_id'                  => $this->id,
            'client_id'                 => $this->client_id,
            'client'                    => optional($this->client)->name,
            'delivery_man_id'           => $this->delivery_man_id,
            'delivery_man'              => optional($this->delivery_man)->name,
            'total_amount'              => $this->total_amount,
            'pickup_date_time'          => $this->pickup_datetime,
            'delivery_date_time'        => $this->delivery_datetime,
            'commission_type'           => optional($this->city)->commission_type,
            'admin_commission'          => optional($this->payment)->admin_commission,
            'delivery_man_commission'   => optional($this->payment)->delivery_man_commission,
            'created_at'                => $this->created_at,
            'updated_at'                => $this->updated_at,
        ];
    }
}