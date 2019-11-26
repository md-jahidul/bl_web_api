<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductDetail extends Model
{
    /**
     * Get json to array
     * @var array
     */
    protected $casts = [
        'other_attributes' => 'array'
    ];
}
