<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Partner;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PartnerOffer extends Model
{
    protected $hidden = ['created_at','updated_at'];

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function partner_offer_details()
    {
        return $this->hasOne(PartnerOfferDetail::class);
    }

    public function offer_category(): BelongsTo
    {
        return $this->belongsTo(PartnerCategory::class, 'partner_category_id', 'id');
    }

    protected $casts = [
        'other_attributes' => 'array'
    ];
}
