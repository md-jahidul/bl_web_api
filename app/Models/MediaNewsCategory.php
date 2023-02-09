<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaNewsCategory extends Model
{
    //protected $guarded = ['id'];
    protected $hidden = ['id', 'url_slug_en', 'url_slug_bn', 'display_order', 'created_at', 'updated_at', 'status'];
}
