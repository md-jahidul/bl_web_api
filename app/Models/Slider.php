<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    public function sliderImages(){
        return $this->hasMany(SliderImage::class);
    }
}
