<?php

namespace App\Models\BlLab;

use Illuminate\Database\Eloquent\Model;

class BlLabProgram extends Model
{
    protected $fillable = ['name_en', 'icon', 'slug', 'display_order', 'is_clickable', 'status'];
}
