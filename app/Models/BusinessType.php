<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BusinessType extends Model
{
    protected $table = 'business_types';

    public function businessTypeDatas(): HasMany
    {
        return $this->hasMany(BusinessTypeData::class,'business_type_id');
    }
}
