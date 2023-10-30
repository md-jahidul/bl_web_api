<?php

namespace App\Models\Page;
use Illuminate\Database\Eloquent\Model;

class NewPage extends Model
{
    public function pageComponentsQuery(){
        return $this->hasMany(NewPageComponent::class, 'page_id', 'id')->orderBy('order', 'asc');
    }
}
