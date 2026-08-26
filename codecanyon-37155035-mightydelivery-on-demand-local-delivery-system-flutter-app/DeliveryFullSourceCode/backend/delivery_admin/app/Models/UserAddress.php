<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'country_id', 'city_id', 'address','address_type', 'latitude', 'longitude', 'contact_number'];

    protected $casts = [
        'user_id'       => 'integer',
        'country_id'    => 'integer',
        'city_id'       => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id', 'id');
    }

    public function scopeMyAddress($query)
    {
        $user = auth()->user();

        if (in_array($user->user_type, ['admin'])) {
            return $query;
        }
        if($user->user_type == 'client') {
            return $query->where('user_id', $user->id);
        }
        return $query;
    }
}
