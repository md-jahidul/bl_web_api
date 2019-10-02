<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlSlider extends Model
{
    protected $casts = [
        'other_attributes' => 'array'
    ];

    public function sliderImages(){
        return $this->hasMany(AlSliderImage::class);
    }
}
