<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FourGLandingPage extends Model
{
    protected $casts = [
        'items' => 'array'
    ];
}
