<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SupportchatHistoryResource extends JsonResource
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
            'support_id'         => $this->support_id,
            'user_id'            => $this->user_id,
            'user_name'          => optional($this->user)->name,
            'support_type'       => $this->customersupport->support_type,
            'message'            => $this->customersupport->message,
            'resolution_detail'  => $this->customersupport->resolution_detail,
            'status'             => $this->customersupport->status,
            'created_at'         => $this->created_at,
            'updated_at'         => $this->updated_at,
        ];
    }
}