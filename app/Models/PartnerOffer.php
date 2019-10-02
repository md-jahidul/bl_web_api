<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Partner;

class PartnerOffer extends Model
{
    protected $hidden = ['created_at','updated_at'];

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }
}
