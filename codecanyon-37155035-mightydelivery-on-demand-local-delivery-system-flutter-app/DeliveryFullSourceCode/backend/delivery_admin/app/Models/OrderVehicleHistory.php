<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderVehicleHistory extends Model
{
    use HasFactory;
    protected $table = 'order_vehicle_histories';
    protected  $fillable = ['order_id','delivery_man_id','vehicle_info'];

    public function deliveryman()
    {
        return $this->belongsTo(User::class,'delivery_man_id','id');
    }
}


