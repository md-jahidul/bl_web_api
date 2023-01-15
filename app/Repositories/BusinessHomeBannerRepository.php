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
        $data = [];
        $banners = $this->model->get();
        $count = 0;
        foreach($banners as $v){
            $data[$count]['image'] = $v->image_name == "" ? "" : $v->image_name;
            $data[$count]['image_mobile'] = $v->image_name_mobile == "" ? "" : $v->image_name_mobile;
            $data[$count]['alt_text'] = $v->alt_text;
            $data[$count]['sort'] = $v->home_sort == 1 ? 'left' : 'right';
            $count++;
        }
        return $data;
    }
    
   


}
