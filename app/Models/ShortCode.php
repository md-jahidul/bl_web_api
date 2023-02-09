<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShortCode extends Model
{
    protected $casts = [
        'other_attributes' => 'array'
    ];

    protected $hidden =['other_attributes'];
}
