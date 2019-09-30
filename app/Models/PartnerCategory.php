<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Partner;

class PartnerCategory extends Model
{
    public function partner()
    {
       return $this->hasOne(Partner::class);
    }
}
