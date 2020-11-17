<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CorporateCrStrategySection extends Model
{
    public function components()
    {
        return $this->hasMany(CorpCrStrategyComponent::class, 'section_id', 'id');
    }
}
