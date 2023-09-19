<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MyPlanProduct extends Model
{
    protected $fillable = [
        'sim_type',
        'content_type',
        'product_code',
        'renew_product_code',
        'recharge_product_code',
        'sms_volume',
        'minute_volume',
        'data_volume',
        'data_volume_unit',
        'validity',
        'validity_unit',
        'tag',
        'display_sd_vat_tax_en',
        'display_sd_vat_tax_bn',
        'points',
        'market_price',
        'discount_price',
        'savings_amount',
        'discount_percentage',
        'is_active',
        'is_default',
    ];
}
