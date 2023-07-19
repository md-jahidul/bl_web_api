<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlLabPersonalInfo extends Model
{
    protected $fillable = [
        'bl_lab_app_id',
        'name',
        'gender',
        'email',
        'phone_number',
        'institute_or_org',
        'education',
        'cv',
        'team_members',
        'applicant_agree',
        'status',
    ];

    protected $casts = [
        'cv' => 'array',
        'team_members' => 'array'
    ];
}
