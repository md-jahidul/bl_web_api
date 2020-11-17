<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CorpInitiativeTabComponent extends Model
{
    protected $casts = [
        'multiple_attributes' => 'array'
    ];
}
