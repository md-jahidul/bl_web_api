<?php

/**
 * Created by PhpStorm.
 * User: bs-205
 * Date: 18/08/19
 * Time: 17:07
 */

namespace App\Repositories;

use App\Models\FourGLandingPage;
use App\Models\MediaLandingPage;

class FourGLandingPageRepository extends BaseRepository
{
    public $modelName = FourGLandingPage::class;

    public function getBannerImage()
    {
        return $this->model->where('component_type', 'banner_image')
            ->select('items')
            ->first();
    }

    public function getComponent($type)
    {
        return $this->model->where('component_type', $type)
            ->first();
    }
}
