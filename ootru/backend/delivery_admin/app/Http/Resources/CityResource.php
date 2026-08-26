<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CityResource extends JsonResource
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
            'name'              => $this->name,
            'address'           => $this->address,
            'country_id'        => (int)$this->country_id,
            'country_name'      => optional($this->country)->name,
            'country'           => $this->country,
            'status'            => (int)$this->status,
            'fixed_charges'     => (int)$this->fixed_charges,
            'extra_charges'     => $this->extraChargesActive,
            'cancel_charges'    => (int)$this->cancel_charges,
            'min_distance'      => (int)$this->min_distance,
            'min_weight'        => (int)$this->min_weight,
            'per_distance_charges' => (int)$this->per_distance_charges,
            'per_weight_charges' => (int)$this->per_weight_charges,
            'created_at'         => $this->created_at,
            'updated_at'         => $this->updated_at,
            'deleted_at'         => $this->deleted_at,
            'commission_type'    => $this->commission_type,
            'admin_commission'   => (int)$this->admin_commission,
        ];
    }
}
