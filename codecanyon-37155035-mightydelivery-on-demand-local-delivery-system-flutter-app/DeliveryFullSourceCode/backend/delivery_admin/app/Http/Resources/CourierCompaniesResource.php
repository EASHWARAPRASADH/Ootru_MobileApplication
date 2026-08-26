<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CourierCompaniesResource extends JsonResource
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
            'id'                            => $this->id,
            'name'                          => $this->name,
            'link'                          => $this->link,
            'tracking_details'              => $this->tracking_details,
            'tracking_number'               => $this->tracking_number,
            'shipping_provider'             => $this->shipping_provider,
            'date_shipped'                  => $this->date_shipped,
            'image'                         => getSingleMediaSettingImage($this, 'couriercompanies_image',null),
            'created_at'                    => $this->created_at,
            'updated_at'                    => $this->updated_at,
        ];
    }
}
