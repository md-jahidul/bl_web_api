<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FrontEndDynamicRoute extends Model
{
    use SoftDeletes;

    protected $casts = [
        'children' => 'array'
    ];
}
