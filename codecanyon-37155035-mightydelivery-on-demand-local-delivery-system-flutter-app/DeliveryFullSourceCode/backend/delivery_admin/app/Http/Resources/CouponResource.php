<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return[
            'id'   => $this->id,
            'coupon_code' => $this->coupon_code,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'value_type' => $this->value_type,
            'discount_amount' => $this->discount_amount,
            'city_type' =>$this->city_type,
            'city_id' => $this->city_id,
            'status'  => $this->status,
            'created_at' =>$this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at'=> $this->deleted_at,
        ];
    }
}
