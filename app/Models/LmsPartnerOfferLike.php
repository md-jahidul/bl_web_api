<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsPartnerOfferLike extends Model
{
    protected $fillable = ['offer_id', 'like', 'type'];
}
