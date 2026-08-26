<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class CourierCompanies extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    protected $fillable = ['name','link','status','tracking_details','tracking_number','shipping_provider','date_shipped'];

    public function orders()
    {
        return $this->hasMany(Order::class,'couriercompany_id','id');
    }
}
