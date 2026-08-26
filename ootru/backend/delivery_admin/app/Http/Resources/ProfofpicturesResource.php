<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProfofpicturesResource extends JsonResource
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
            'id'                        => (int)$this->id,
            'order_id'                  => (int)$this->order_id,
            'type'                      => $this->type,
            'prof_file'                 => getAttachments($this->getMedia('prof_file')),
            // 'prof_file'              => getAttachmentArray($this->getMedia('prof_file')),            
            // 'prof_file'              => $this->getMedia('prof_file')->map(function ($media) {
            //                                 return  $media->getUrl();
            //                             }),
            'created_at'                => $this->created_at,
            'updated_at'                => $this->updated_at,
        ];
    }
}
