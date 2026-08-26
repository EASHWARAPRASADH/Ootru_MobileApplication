<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;


class CustomerSupport extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    protected $fillable =['message','support_type','user_id','order_id','status','resolution_detail'];

    protected $casts = [
        'user_id' => 'integer',
        'order_id' => 'integer',
    ];
    public function user(){
        return $this->belongsTo(User::class, 'user_id','id');
    }

    public function order(){
        return $this->belongsTo(Order::class, 'order_id','id');
    }
    
    
    public function supportchathistory() {
        return $this->hasMany( SupportChathistory::class, 'support_id', 'id');
    }
    public function chathistory()
    {
        return $this->hasOne(SupportChathistory::class, 'support_id');
    }
    public function scopeMyCustomersupport($query){
        $user = auth()->user();
        if($user->user_type == 'admin') {
            return $query;
        }
        if($user->user_type == 'client') {  
            return $query->where('user_id', $user->id);
        }
        return $query;
    }

    protected static function boot(){
        parent::boot();
        static::deleted(function ($row) {
            $row->supportchathistory()->delete();
        });
    }

}