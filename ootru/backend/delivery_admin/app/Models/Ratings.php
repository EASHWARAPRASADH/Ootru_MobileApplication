<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ratings extends Model
{
    use HasFactory;

    protected $fillable = [ 'user_id', 'review_user_id', 'order_id', 'rating', 'comment', 'rating_by' ];

    protected $casts = [
        'user_id'   => 'integer',
        'review_user_id'          => 'integer',
        'order_id'         => 'integer',
        'rating'            => 'double',
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id')->withTrashed();
    }
}
