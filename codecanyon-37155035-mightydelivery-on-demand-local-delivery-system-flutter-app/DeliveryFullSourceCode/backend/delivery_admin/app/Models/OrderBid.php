<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderBid extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id','delivery_man_id','bid_amount','notes','is_bid_accept'
    ];
    
    protected $casts = [
        'is_bid_accept' => 'integer'
    ];
    public function deliveryMan()
    {
        return $this->belongsTo(User::class, 'delivery_man_id'); 
    }
    public function scopeMyBid($query){
        $user = auth()->user();
        if($user->user_type == 'admin') {
            return $query;
        }
        if($user->user_type == 'delivery_man') {  
            return $query->where('delivery_man_id', $user->id);
        }
        return $query;
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
