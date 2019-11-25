<?php

namespace App\Repositories;

use App\Models\Banner;

/**
 * Class BannerRepository
 * @package App\Repositories
 */
class BannerRepository
{
    /**
     * @var Banner
     */
    protected $model;


    /**
     * BannerRepository constructor.
     * @param Banner $model
     */
    public function __construct(Banner $model)
    {
        $this->model = $model;
    }

    /**
     * Retrieve Banner info
     *
     * @return mixed
     */
    public function getBannerInfo()
    {
        return $this->model->get();
    }
}
