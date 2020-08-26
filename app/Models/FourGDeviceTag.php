<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FourGDeviceTag extends Model
{
    protected $hidden = ['pivot', 'tag_color', 'alias', 'created_at', 'updated_at'];
}
