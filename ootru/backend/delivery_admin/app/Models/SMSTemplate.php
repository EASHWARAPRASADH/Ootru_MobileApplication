<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SMSTemplate extends Model
{
    use HasFactory;
    protected $fillable = ['subject','sms_description','sms_id','type','order_status'];

    public function smsSetting()
    {
        return $this->belongsTo(SMSSetting::class, 'sms_id');
    }
}
