<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\PartnerOffer;
use App\Models\PartnerCategory;

class Partner extends Model
{

    protected $casts = [
        'other_attributes' => 'array'
    ];

    public function partnerCategory()
    {
       return $this->belongsTo(PartnerCategory::class);
    }

    public function partnerOffers()
    {
        return $this->hasMany(PartnerOffer::class);
    }
}
