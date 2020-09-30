<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CorpCrStrategyComponent extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'other_attributes' => 'array'
    ];

    public function components()
    {
        return $this->hasMany(Component::class, 'section_details_id', 'id');
    }
}
