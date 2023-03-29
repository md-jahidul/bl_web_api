<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuickLaunchItem extends Model
{
    protected $casts = [
        'other_attributes' => 'array'
    ];

}
