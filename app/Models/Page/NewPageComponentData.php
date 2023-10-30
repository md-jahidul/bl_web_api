<?php

namespace App\Models\Page;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewPageComponentData extends Model
{
    protected $guarded = ['id'];

    public function children(){
        return $this->hasMany( NewPageComponentData::class, 'parent_id', 'id' );
    }
}
