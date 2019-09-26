<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlSlider extends Model
{
    public function sliderImages(){
        return $this->hasMany(AlSliderImage::class);
    }
}
