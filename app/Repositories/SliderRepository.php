<?php

namespace App\Repositories;

use App\Models\Slider;

/**
 * Class SliderRepository
 * @package App\Repositories
 */
class SliderRepository extends BaseRepository
{
    /**
     * @var model
     */
    protected $model;


    /**
     * SliderRepository constructor.
     * @param Slider $model
     */
    public function __construct(Slider $model)
    {
        $this->model = $model;
    }


    /**
     * Retrieve Slider info
     *
     * @return mixed
     */
    public function getHomeSliderInfo()
    {
        $data = $this->model::whereHas('sliderImages', function ($q) {
            $q->where('is_active', 1);
        })->with(['sliderImages' => function ($q) {
            $q->where('is_active', 1);
        }])->where('platform', "App")
            ->where('component_id', 1)
            ->get();

        return $data;
    }


    /**
     * Retrieve Slider info
     *
     * @return mixed
     */
    public function getDashboardSliderInfo()
    {
        $data = $this->model::whereHas('sliderImages', function ($q) {
            $q->where('is_active', 1);
        })->with(['sliderImages' => function ($q) {
            $q->where('is_active', 1);
        }])->where('platform', "App")
            ->where('component_id', 2)
            ->get();

        return $data;
    }
}
