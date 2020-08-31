<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadRequest extends Model
{
    protected $fillable = [
        'lead_category_id',
        'lead_product_id',
        'form_data',
        'lead_product_type',
    ];

    protected $casts = [
        'form_data' => 'array'
    ];
}
