<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WebsiteSectionTitle extends Model
{
    use HasFactory;

    protected $fillable = ['section_id', 'title'];

    public function websitesection()
    {
        return $this->belongsTo(WebsiteSection::class,'section_id');
    }
}
