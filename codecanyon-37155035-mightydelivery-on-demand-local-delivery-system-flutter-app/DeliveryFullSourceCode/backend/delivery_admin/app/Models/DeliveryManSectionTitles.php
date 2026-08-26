<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryManSectionTitles extends Model
{
    protected $fillable = ['section_id', 'title'];

    public function deliverymansection()
    {
        return $this->belongsTo(DeliveryManSection::class,'section_id');
    }
}
