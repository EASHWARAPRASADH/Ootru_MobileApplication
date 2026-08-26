<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Claims extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;
    protected $fillable = ['traking_no', 'prof_value', 'detail', 'status','client_id','isClaimed'];

    public function user()
    {
      return  $this->belongsTo(User::class,'client_id','id');
    }
    public function order()
    {
        return $this->belongsTo(Order::class, 'traking_no', 'milisecond');
    }
    public function claimsHistory()
    {
        return $this->hasMany(ClaimsHistory::class,'claim_id','id');
    }
    public function scopeMyClaims($query){
        $user = auth()->user();
        if($user->user_type == 'admin') {
            return $query;
        }
        if($user->user_type == 'client') {  
            return $query->where('client_id', $user->id);
        }
        return $query;
    }
}
