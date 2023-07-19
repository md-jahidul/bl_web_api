<?php

namespace App\Models;

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
}
