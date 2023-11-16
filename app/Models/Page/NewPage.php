<?php

namespace App\Models\Page;
use Illuminate\Database\Eloquent\Model;
use App\Models\Page\NewPageComponent;

class NewPage extends Model
{
    protected $primaryKey = 'id';

    public function pageComponentsQuery(){
        return $this->hasMany(NewPageComponent::class, 'page_id', 'id')->select('id', 'name', 'type')->orderBy('order', 'asc');
    }
}
