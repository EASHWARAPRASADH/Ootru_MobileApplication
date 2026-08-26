<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WithdrawDetailResource extends JsonResource
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
            'id'                => $this->id,
            'transaction_id'    => $this->transaction_id,
            'via'               => $this->via,
            'other_detail'       => $this->other_detail,
            'withdrawdetail_image'     => getSingleMedia($this, 'withdrawimage',null),
            'created_at'         => $this->created_at,
            'updated_at'         => $this->updated_at,
        ];
    }
}