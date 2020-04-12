<?php

namespace App\Repositories;

use App\Models\AlSlider;
use App\Models\Slider;

/**
 * Class SliderRepository
 * @package App\Repositories
 */
class SliderRepository extends BaseRepository
{

    public $modelName = AlSlider::class;


    /**
     * Retrieve Slider info
     *
     * @return mixed
     */
    public function getSliderInfo($slider)
    {
        return $this->model->where('short_code', $slider)
//            ->with(['sliderImages' => function ($q) {
//                $q->where('is_active', 1);
//            }])
            ->first();
    }


    /**
     * Retrieve Slider info
     *
     * @return mixed
     */
    public function getDashboardSliderInfo()
    {
        $data = $this->model->whereHas('sliderImages', function ($q) {
            $q->where('is_active', 1);
        })->with(['sliderImages' => function ($q) {
            $q->where('is_active', 1);
        }])->where('platform', "App")
            ->where('component_id', 2)
            ->get();

        return $data;
    }
}
