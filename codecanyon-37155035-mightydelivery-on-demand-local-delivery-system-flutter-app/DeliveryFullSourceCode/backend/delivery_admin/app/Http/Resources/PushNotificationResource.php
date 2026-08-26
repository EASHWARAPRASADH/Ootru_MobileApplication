<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PushNotificationResource extends JsonResource
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
            'id'     => $this->id,
            'title'  => $this->title,
            'message'  => $this->message,
            'for_client'  => $this->for_client,
            'for_delivery_man'  => $this->for_delivery_man,
            'for_all'  => $this->for_all,
            'notification_count'  => $this->notification_count,
            'notification_image' =>getSingleMedia($this, 'notification_image',null),
            'created_at'  => $this->created_at,
            'updated_at'  => $this->updated_at,
        ];
    }
}
