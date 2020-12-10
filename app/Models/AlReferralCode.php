<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlReferralCode extends Model
{
    protected $fillable = ['app_id', 'mobile_no', 'referral_code', 'share_count'];
}
