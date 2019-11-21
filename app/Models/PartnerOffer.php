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

    public function partner_offer_details()
    {
        return $this->hasOne(PartnerOfferDetail::class);
    }

//    public function scopeSelcetColumn($query, $column)
//    {
//        return $query->whereHas('partner', function ($q) use ($column) {
//            $q->select($column);
//        });
//    }
}
