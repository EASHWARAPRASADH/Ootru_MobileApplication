<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['coupon_code','start_date','end_date','value_type','discount_amount','city_type','city_id','status'];

    public function getCityIdAttribute($value)
    {
        $val = isset($value) ? json_decode($value) : null;
        return $val;
    }
}
