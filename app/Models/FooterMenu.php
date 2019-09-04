<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class FooterMenu extends Model
{

    public function parent(){
        return $this->hasOne( FooterMenu::class, 'id', 'parent_id' );
    }

    public function children(){
        return $this->hasMany( FooterMenu::class, 'parent_id', 'id' );
    }
}
