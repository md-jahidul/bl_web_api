<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\SimCategory;

class Product extends Model
{
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
}
