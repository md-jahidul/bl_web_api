<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubFooter extends Model
{
    protected $table = 'sub_footers';

    protected $casts = [
        'other_attributes' => 'array'
    ];

}
