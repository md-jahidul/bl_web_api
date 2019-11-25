<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\SimCategory;

class Product extends Model
{
    protected $casts = [
        'offer_info' => 'array'
    ];

    public function sim_category()
    {
        return $this->belongsTo(SimCategory::class);
    }

    public function scopeCategory($query, $type)
    {
        return $query->whereHas('sim_category', function ($q) use ($type) {
            $q->where('alias', $type);
        });
    }

    public function product_details()
    {
        return $this->hasOne(ProductDetail::class);
    }

    public function related_product()
    {
        return $this->hasMany(RelatedProduct::class);
    }

    public function other_related_product()
    {
        return $this->hasMany(OtherRelatedProduct::class, $this->offer_info['other_offer_type_id']);
    }
}
