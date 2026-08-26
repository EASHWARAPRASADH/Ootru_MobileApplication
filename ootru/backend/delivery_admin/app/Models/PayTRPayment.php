<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayTRPayment extends Model
{
    use HasFactory;
    protected $fillable = [ 'merchant_oid', 'client_id', 'merchant_id', 'hash', 'datetime', 'total_amount','payment_type','payment_status' ];
}
