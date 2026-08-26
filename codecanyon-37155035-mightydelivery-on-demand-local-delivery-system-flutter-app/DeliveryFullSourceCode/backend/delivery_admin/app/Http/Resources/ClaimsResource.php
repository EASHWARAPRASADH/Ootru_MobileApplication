<?php

namespace App\Http\Resources;

use App\Models\Claims;
use Illuminate\Http\Resources\Json\JsonResource;

class ClaimsResource extends JsonResource
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
            'id'                  => $this->id,
            'client_id'           => $this->client_id,
            'client_name'         => optional($this->user)->name, 
            'traking_no'          => $this->traking_no,
            'prof_value'          => $this->prof_value,
            'detail'              => $this->detail,
            'status'              => $this->status,
            'attachment_file'     => getAttachments($this->getMedia('attachment_file')),
        //    'claims_history'      => $this->claimsHistory,
            'claims_history' => $this->claimsHistory ? ClaimsHistoryResource::collection($this->claimsHistory) : null,

           'created_at'          => $this->created_at,
           'updated_at'          => $this->updated_at,
        ];
    }
}