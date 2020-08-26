<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlFaqCategory extends Model
{
    public function faqs()
    {
        return $this->hasMany(AlFaq::class, 'slug', 'slug');
    }
}
