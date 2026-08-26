<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reschedule extends Model
{
    use HasFactory;
    protected $fillable =['order_id','date','reason'];

    public function orders(){
        return $this->hasMany(Order::class,'order_id','id');
    }
}
