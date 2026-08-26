<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RestApiResource extends JsonResource
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
            'id'               => $this->id,
            'name'             => $this->name,
            'rest_key'         => $this->rest_key,
            'description'      => $this->description,
            'country_id'       => $this->country_id,
            'country_name'     => optional($this->country)->name,
            'city_id'          => $this->city_id,
            'city_name'        => optional($this->city)->name,
            'last_access_date' =>  $this->last_access_date,
            'created_at'       => $this->created_at,
            'updated_at'       => $this->updated_at,
            'deleted_at'       => $this->deleted_at,
        ];
    }
}
