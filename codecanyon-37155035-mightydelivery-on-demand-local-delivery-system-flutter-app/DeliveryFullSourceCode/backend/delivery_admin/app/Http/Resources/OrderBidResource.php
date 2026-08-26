<?php

namespace App\Http\Resources;

use App\Models\Order;
use Illuminate\Http\Resources\Json\JsonResource;
use PhpParser\Node\Expr\Cast\Double;

class OrderBidResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $orders = Order::where('id',$this->order_id)->get();
        foreach($orders as $order){
            $orderList = $order->total_amount;
        }
        return [
            'id'                    => (int)$this->id,
            'order_id'              => (int)$this->order_id,
            'bid_amount'            => (Double)$this->bid_amount,
            'notes'                 => $this->notes,
            'is_bid_accept'         => $this->is_bid_accept,
            'total_amount'          => (double)$orderList,
            'created_at'            => $this->created_at,
            'updated_at'            => $this->updated_at,
        ];
    }
}
