<?php

namespace App\Models\Page;
use Illuminate\Database\Eloquent\Model;
use App\Models\Page\NewPageComponent;

class NewPage extends Model
{
    protected $primaryKey = 'id';
    protected $hidden = ['created_at', 'updated_at'];

    public function pageComponentsQuery(){
        return $this->hasMany(NewPageComponent::class, 'page_id', 'id')->select('id', 'name', 'type')->orderBy('order', 'asc');
    }
}
