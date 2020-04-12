<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    public function parent(){
        return $this->hasOne( Menu::class, 'id', 'parent_id' );
    }

    public function children(){
        return $this->hasMany( Menu::class, 'parent_id', 'id' )->orderBy('display_order');
    }

    public function scopeActive($query)
    {
//        return $query->whereHas();
    }
}
