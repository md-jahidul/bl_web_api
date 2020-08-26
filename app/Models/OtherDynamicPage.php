<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtherDynamicPage extends Model
{
    protected $table = 'other_dynamic_page';

    public function components()
    {
        return $this->hasMany(Component::class, 'section_details_id', 'id');
    }
}
