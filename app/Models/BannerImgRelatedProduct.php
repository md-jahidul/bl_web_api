<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BannerImgRelatedProduct extends Model
{
    protected $casts = [
        'related_product_id' => 'array'
    ];
}
