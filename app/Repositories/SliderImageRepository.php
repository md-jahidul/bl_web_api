<?php

namespace App\Repositories;

use App\Models\AlSlider;
use App\Models\AlSliderImage;
use App\Models\Slider;

/**
 * Class SliderRepository
 * @package App\Repositories
 */
class SliderImageRepository extends BaseRepository
{
    public $modelName = AlSliderImage::class;


    public function aboutUsSliders($sliderId)
    {
        return $this->model->where('slider_id', $sliderId)
            ->where('is_active', 1)
            ->orderBy('display_order')
            ->get();
    }
}
