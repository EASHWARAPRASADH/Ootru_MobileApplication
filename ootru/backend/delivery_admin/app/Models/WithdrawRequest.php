<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WithdrawRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'amount', 'currency', 'status'
    ];

    protected $casts = [
        'user_id'   => 'integer',
        'amount'    => 'double',
    ];
    

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function withdrawRequest()
    {
        return $this->hasOne(WithdrawDetail::class, 'withdrawrequest_id');
    }

    public function scopemyWithdrawRequest($query)
    {
        $user = auth()->user();

        if($user->user_type == 'admin'){
            $query = $query;
        } else {
            $query = $query->where('user_id', $user->id);
        }

        return $query;
    }
}