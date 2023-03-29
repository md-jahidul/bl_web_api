<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Partner;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PartnerCategory extends Model
{
    public function partner()
    {
       return $this->hasOne(Partner::class);
    }

    public function partnerOffers(): HasMany
    {
        return $this->hasMany(PartnerOffer::class);
    }
}
