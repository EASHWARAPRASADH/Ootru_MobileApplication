<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WalletHistory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [ 'user_id', 'type', 'transaction_type', 'currency', 'amount', 'balance', 'datetime', 'order_id', 'description', 'data' ];

    protected $casts = [
        'user_id'   => 'integer',
        'amount'    => 'double',
        'balance'   => 'double',
        'order_id'  => 'integer',
    ];
    
    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function wallet_user() {
        return $this->hasOne(Wallet::class, 'user_id', 'user_id');
    }
    
    public function scopemyWalletHistory($query)
    {
        $user = auth()->user();

        if($user->user_type == 'admin'){
            $query = $query;
        } else {
            $query = $query->where('user_id', $user->id);
        }

        return  $query;
    }

    public function getDataAttribute($value)
    {
        $val = isset($value) ? json_decode($value, true) : null;
        return $val;
    }

    public function setDataAttribute($value)
    {
        $this->attributes['data'] = isset($value) ? json_encode($value) : null;
    }
}
