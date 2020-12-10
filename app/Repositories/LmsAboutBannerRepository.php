<?php

/**
 * Created by PhpStorm.
 * User: BS23
 * Date: 27-Aug-19
 * Time: 3:51 PM
 */

namespace App\Repositories;

use App\Models\AboutPage;
use App\Models\LmsAboutBannerImage;
use App\Models\MediaBannerImage;
use App\Models\Prize;

class LmsAboutBannerRepository extends BaseRepository
{
    public $modelName = LmsAboutBannerImage::class;


    public function bannerUpload($data)
    {
        return $this->model->updateOrCreate(['page_type' => $data['page_type']], $data);
    }
}
