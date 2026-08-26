<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class DeliverymanVehicleHistory extends Model implements HasMedia
{
    use HasFactory,InteractsWithMedia;
    protected $fillable = ['delivery_man_id','start_datetime','end_datetime','is_active','vehicle_info'];

    public function delievryman()
    {
        return $this->belongsTo(User::class,'delivery_man_id','id');
    }
}
