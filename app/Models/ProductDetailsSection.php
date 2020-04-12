<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductDetailsSection extends Model
{
    protected $guarded = ["*"];

    protected $casts = [
        'other_attributes' => 'array'
    ];

    public function components()
    {
        return $this->hasMany(Component::class, 'section_details_id', 'id');
    }
}
