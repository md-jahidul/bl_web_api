<?php

namespace App\Repositories;

use App\Models\ExploreC;

/**
 * Class ExploreCRepository
 * @package App\Repositories
 */
class ExploreCRepository extends BaseRepository
{
    protected $modelName = ExploreC::class;

    public function getExploreC()
    {
        return $this->model->where('status', 1)->orderBy('display_order', 'ASC')->get();
    }

    public function findOneBySlug($slug)
    {
        return $this->model->where('slug_en', $slug)->orWhere('slug_bn', $slug)->select('id')->first();
    }
}
