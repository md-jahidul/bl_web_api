<?php

namespace App\Models\BlLab;

use Illuminate\Database\Eloquent\Model;

class BlLabSummary extends Model
{
    protected $fillable = [
        'bl_lab_app_id',
        'idea_title',
        'idea_details',
        'industry',
        'apply_for',
        'status',
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
