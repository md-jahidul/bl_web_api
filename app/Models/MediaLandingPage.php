<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaLandingPage extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'items' => 'array'
    ];
}
