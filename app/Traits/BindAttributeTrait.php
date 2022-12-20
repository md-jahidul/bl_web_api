<?php
namespace App\Traits;

trait BindAttributeTrait {
    /**
     * @param object,array
     * @return array
     */
    public function bindAttribute(object $attribute,Array $arr) : array{
        if($attribute->other_attributes){
            foreach ($attribute->other_attributes as $key => $value) {
                $arr[$key] = $value;
            }
        }
        return $arr;
    }
}
