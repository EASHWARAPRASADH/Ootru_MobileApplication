<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia, SoftDeletes;

    protected $fillable = [ 'title', 'type', 'size', 'capacity', 'city_ids', 'status', 'description','price','min_km','per_km_charge'];

    protected $casts = [
        'status' => 'integer',
        'per_km_charge' => 'double',
        'price' => 'double',
    ];

    public function getCityIdsAttribute($value)
    {
        $val = isset($value) ? json_decode($value) : null;
        return $val;
    }

    // public function setCityIdsAttribute($value)
    // {
    //     $this->attributes['city_ids'] = isset($value) ? json_decode($value) : null;
    // }
}
