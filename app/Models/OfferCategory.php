<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfferCategory extends Model
{
    public function scopePackageType($query, $type = 'prepaid')
    {
        return  (strtolower($type) == 'prepaid') ? $query->where('parent_id', 0) : $query->whereIn('alias', ['internet', 'packages', 'others'])->where('parent_id', 0);
    }

    public function type()
    {
        return $this->belongsTo(SimCategory::class);
    }
}
