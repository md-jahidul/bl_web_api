<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessTypeData extends Model
{
    protected $table = "business_type_datas";

    protected $casts = [
        'other_attributes' => 'array'
    ];
}
