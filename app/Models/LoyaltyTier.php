<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LoyaltyTier extends Model
{
    public function partnerOffers(): HasMany
    {
        return $this->hasMany(PartnerOffer::class);
    }

    public function partner(): HasOne
    {
        return $this->hasOne(Partner::class);
    }
}
