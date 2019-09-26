<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlSliderImage extends Model
{
    public function slider(){
        return $this->belongsTo(AlSlider::class);
    }
}
