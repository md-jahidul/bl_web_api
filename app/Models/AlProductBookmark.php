<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlProductBookmark extends Model
{
    protected $fillable = ['module_type', 'category', 'mobile', 'product_id'];
}
