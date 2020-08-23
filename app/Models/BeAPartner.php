<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeAPartner extends Model
{
    protected $casts = [
       'banner_image' => 'array'
    ];
}
