<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RestApi extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable =['name','description','country_id','city_id','rest_key','last_access_date'];

    protected $casts = [
        'country_id' => 'integer',
        'city_id' => 'integer',  
    ];

    public function country(){
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    public function city(){
        return $this->belongsTo(City::class, 'city_id', 'id');
    }

    public function scopeMyRestApi($query)
    {
        $user = auth()->user();
        if (in_array($user->user_type, ['admin'])) {
            return $query;
        }

        if ($user->user_type == 'client') {
            return $query->where('city_id', $user->city_id);
        }

        return $query;
    }

}
