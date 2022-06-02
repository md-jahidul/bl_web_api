<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MyBlProduct extends Model
{
    protected $guarded = ['id'];

    public function details()
    {
        return $this->belongsTo(ProductCore::class, 'product_code', 'product_code');
    }
}
