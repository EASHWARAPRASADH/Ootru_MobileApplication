<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Emergency extends Model
{
    use HasFactory;

    protected $fillable = [ 'delivery_man_id', 'datetime', 'emrgency_reason','status','emergency_resolved'];


    public function deliveryMan(){
        return $this->belongsTo(User::class, 'delivery_man_id', 'id');
    }
}
