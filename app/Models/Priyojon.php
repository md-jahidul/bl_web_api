<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Priyojon extends Model
{

    public function children()
    {
        return $this->hasMany(Priyojon::class, 'parent_id', 'id');
    }
}
