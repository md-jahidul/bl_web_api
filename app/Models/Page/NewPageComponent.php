<?php

namespace App\Models\Page;

use Illuminate\Database\Eloquent\Model;

class NewPageComponent extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'attribute' => 'array',
        'config' => 'array'
    ];

    public function componentData()
    {
        return $this->hasMany(NewPageComponentData::class,  'component_id', 'id');
    }
}
