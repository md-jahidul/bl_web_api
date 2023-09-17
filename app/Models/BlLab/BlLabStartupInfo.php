<?php

namespace App\Models\BlLab;

use Illuminate\Database\Eloquent\Model;

class BlLabStartupInfo extends Model
{
    protected $fillable = [
        'bl_lab_app_id',
        'problem_identification',
        'big_idea',
        'target_group',
        'market_size',
        'business_model',
        'business_model_file',
        'gtm_plan',
        'gtm_plan_file',
        'financial_metrics',
        'financial_metrics_file',
        'exist_product_service',
        'exist_product_service_details',
        'exist_product_service_diff',
        'receive_fund',
        'receive_fund_source',
        'startup_current_stage',
        'status'
    ];

    protected $casts = [
        'business_model_file' => 'array',
        'gtm_plan_file' => 'array',
        'financial_metrics_file' => 'array',
    ];
}
