<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    // use HasFactory;
    protected $primaryKey = 'id';

    public function pageComponentsQuery(){
        return null;
        // return $this->hasMany(PageComponent::class, 'page_id', 'id')->select("id","name","type","attribute","config")->where('status', 1)->orderBy('order', 'asc');
    }
}