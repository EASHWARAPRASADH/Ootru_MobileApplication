<?php

namespace App\Http\Resources;

use App\Models\SupportChathistory;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class CustomerSupportResource extends JsonResource
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
            'support_id'             => $this->id,
            'user_id'                => $this->user_id,
            'user_name'              => optional($this->user)->name,
            'order_id'               => $this->order_id,
            'support_type'           => $this->support_type,
            'message'                => $this->message,
            'resolution_detail'      => $this->resolution_detail,
            'status'                 => $this->status,
            'support_image'          => getMediaFileExit($this, 'support_image') ? getSingleMedia($this, 'support_image',null) : null,
            'support_videos'         => getMediaFileExit($this, 'support_videos') ? getSingleMedia($this, 'support_videos',null) : null,
            'supportchathistory'     => $this->getSupportChatHistory(),
            'created_at'             => $this->created_at,
            'updated_at'             => $this->updated_at,
        ];
    }
    protected function getSupportChatHistory()
    {
        $matches = SupportChathistory::where('support_id', $this->id)->orderBy('created_at', 'desc') ->get();
        $chat = [];
    
        foreach ($matches as $match) {
            $userType = optional($match->user)->user_type;
    
            $chat[] = [
                'send_by'   => $userType,
                'message'   => $match->message,
                'datetime' => $match->created_at,
            ];
        }
    
        return $chat;
    }
}