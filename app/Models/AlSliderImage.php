<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlSliderImage extends Model
{
    protected $casts = [
        'image_url' => "LocalHost",
        'other_attributes' => 'array'
    ];

    public function slider(){
        return $this->belongsTo(AlSlider::class);
    }
}
