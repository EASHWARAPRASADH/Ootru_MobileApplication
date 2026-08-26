<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class PushNotification extends Model implements HasMedia
{
    use HasFactory; use InteractsWithMedia;
   

    protected $fillable = ['title', 'message', 'for_client', 'for_delivery_man','for_all','notification_count'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
