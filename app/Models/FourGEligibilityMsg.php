<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FourGEligibilityMsg extends Model
{
    protected $table = 'eligibility_messages';
    protected $casts = ['other_attributes' => 'array'];
}
