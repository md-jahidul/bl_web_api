<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CorpCrStrategyComponent extends Model
{
    protected $casts = [
        'other_attributes' => 'array',
        'banner' => 'array'
    ];

    public function components()
    {
        return $this->hasMany(Component::class, 'section_details_id', 'id');
    }
}
