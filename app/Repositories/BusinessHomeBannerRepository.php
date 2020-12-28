<?php

/**
 * User: Bulbul Mahmud Nito
 * Date: 24/02/2020
 */

namespace App\Repositories;

use App\Models\BusinessHomeBanner;

class BusinessHomeBannerRepository extends BaseRepository {

    public $modelName = BusinessHomeBanner::class;

    public function getHomeBanners() {

        $banners = $this->model->select('image_name', 'image_name_mobile', 'image_name_en', 'image_name_bn', 'alt_text', 'alt_text_bn', 'home_sort')->get();

        return $banners;
    }




}
