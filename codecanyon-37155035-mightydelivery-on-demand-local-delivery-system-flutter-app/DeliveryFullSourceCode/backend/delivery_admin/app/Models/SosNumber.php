<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SosNumber extends Model
{
    use HasFactory;
        protected $fillable = [
            'delivery_man_id','name', 'contact_number'
        ];
}
