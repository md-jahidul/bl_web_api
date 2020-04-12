<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerOfferDetail extends Model
{
    protected $fillable = [
        'partner_offer_id',
        'details_en',
        'details_bn',
        'offer_details_en',
        'offer_details_bn',
        'eligible_customer_en',
        'eligible_customer_bn',
        'avail_en',
        'avail_bn'
    ];
}

