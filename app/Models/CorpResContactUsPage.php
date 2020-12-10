<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CorpResContactUsPage extends Model
{
    protected $guarded = ["id"];

    public function fields()
    {
        return $this->hasMany(CorpResContactUsFields::class, 'page_id', 'id');
    }
}
