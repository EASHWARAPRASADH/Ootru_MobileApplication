<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserAddressResource extends JsonResource
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
            'id'                => (int)$this->id,
            'user_id'           => (int)$this->user_id,
            'user_name'         => optional($this->user)->name,
            'country_id'        => (int)$this->country_id,
            'country_name'      => optional($this->country)->name,
            'city_id'           => (int)$this->city_id,
            'city_name'         => optional($this->city)->name,
            'address_type'      => $this->address_type,
            'address'           => $this->address,
            'latitude'          => $this->latitude,
            'longitude'         => $this->longitude,
            'contact_number'    => $this->contact_number,
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at,
        ];
    }
}