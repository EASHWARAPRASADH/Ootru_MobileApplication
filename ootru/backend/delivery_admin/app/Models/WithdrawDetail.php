<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class WithdrawDetail extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    protected $fillable = [ 'transaction_id','via','other_detail','withdrawrequest_id','datetime'];

    public function withdrawRequest()
    {
        return $this->belongsTo(WithdrawRequest::class, 'withdrawrequest_id','id');
    }
}
