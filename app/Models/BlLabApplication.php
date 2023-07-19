<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlLabApplication extends Model
{
    protected $fillable = ['bl_lab_user_id', 'id_number', 'application_status', 'step_completed'];

    protected $casts = [
        'step_completed' => 'array'
    ];
}
