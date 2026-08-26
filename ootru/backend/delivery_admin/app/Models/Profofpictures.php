<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Profofpictures extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;
    protected $fillable = ['order_id','type'];
    protected $casts = [
        'order_id' => 'integer',
    ];
}   