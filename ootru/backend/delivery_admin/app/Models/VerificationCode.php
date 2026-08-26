<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerificationCode extends Model
{
    use HasFactory;

    protected $fillable = [ 'user_id', 'code', 'datetime' ];

    protected $casts = [
        'user_id'   => 'integer',
    ];
}
