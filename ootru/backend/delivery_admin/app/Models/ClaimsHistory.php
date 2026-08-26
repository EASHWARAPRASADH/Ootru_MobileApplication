<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ClaimsHistory extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;
    protected $fillable = ['claim_id','amount','description'];

    public function claims()
    {
      return  $this->belongsTo(Claims::class,'claim_id','id');
    }
    
    public function scopeMyClaimshistory($query)
    {
        $user = auth()->user();
        
        if ($user->user_type == 'admin') {
            return $query;
        }
        
        if ($user->user_type == 'client') {
            return $query->whereHas('claims', function($query) use ($user) {
                $query->where('client_id', $user->id);
            });
        }
        
        return $query;
    }

}
