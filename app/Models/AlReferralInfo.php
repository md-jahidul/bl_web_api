<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlReferralInfo extends Model
{
    protected $fillable = ['app_id', 'title_en', 'title_bn', 'details_en', 'details_bn'];
}
