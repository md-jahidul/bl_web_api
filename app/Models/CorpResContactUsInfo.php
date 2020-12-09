<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CorpResContactUsInfo extends Model
{
    protected $fillable = ['page_slug', 'contact_field'];

    protected $casts = [
        'contact_field' => 'array'
    ];
}
