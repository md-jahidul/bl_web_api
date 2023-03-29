<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExploreC extends Model
{
    protected $casts = [
        'multiple_attributes' => 'array'
    ];
}
