<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestApiHistory extends Model
{
    use HasFactory;
    protected $fillable =['rest_key','order_id','last_access_date'];
}
