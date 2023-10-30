<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    // use HasFactory;
    protected $primaryKey = 'id';

    public function pageComponentsQuery(){
        return $this->hasMany(PageComponent::class, 'page_id', 'id')->orderBy('order', 'asc');
    }
}
