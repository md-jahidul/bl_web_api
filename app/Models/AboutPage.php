<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutPage extends Model
{
    protected $fillable = [
        'slug',
        'details_en',
        'details_bn',
        'left_side_img',
        'right_side_ing',
        'other_attributes',
    ];

    protected $casts = [
        'other_attributes' => 'array',
    ];
}
