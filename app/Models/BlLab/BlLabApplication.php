<?php

namespace App\Models\BlLab;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class BlLabApplication extends Model
{
    protected $fillable = ['bl_lab_user_id', 'id_number', 'application_status', 'step_completed', 'submitted_at'];

    protected $casts = [
        'step_completed' => 'array'
    ];

    public function summary(): HasOne
    {
        return $this->hasOne(BlLabSummary::class, 'bl_lab_app_id', 'id');
    }
}
