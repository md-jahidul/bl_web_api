<?php

/**
 * User: Bulbul Mahmud Nito
 * Date: 24/02/2020
 */

namespace App\Repositories;

use App\Models\RoamingCategory;

class RoamingCategoryRepository extends BaseRepository {

    public $modelName = RoamingCategory::class;

    public function getCategoryList() {
        $categories = $this->model
                        ->where('status', 1)
                        ->orderBy('sort')->get();
        $data = [];
        $count = 0;
        
        $slugs = array(
            1 => 'offer',
            2 => 'about-roaming',
            3 => 'roaming-rates',
            4 => 'bill-payment',
            5 => 'info-tips',
        );


        foreach ($categories as $v) {
            $data[$count]['id'] = $v->id;
            $data[$count]['category_slug'] = $slugs[$v->id];
            $data[$count]['url_slug'] = $v->url_slug;
            $data[$count]['url_slug_bn'] = $v->url_slug_bn;
            $data[$count]['page_header'] = $v->page_header;
            $data[$count]['page_header_bn'] = $v->page_header_bn;
            $data[$count]['schema_markup'] = $v->schema_markup;
            $data[$count]['name_en'] = $v->name_en;
            $data[$count]['name_bn'] = $v->name_bn;
            $data[$count]['alt_text'] = $v->alt_text;
            $data[$count]['alt_text_bn'] = $v->alt_text_bn;
            $data = $this->prepareImageData($v, $data, $count);

            $count++;
        }
        return $data;
    }

    public function prepareImageData($v, $data, $count) {
        $extension = explode('.', $v['banner_web']);
        $extension = isset($extension[1]) ? ".".$extension[1] : null;
        $fileNameEn = $v['banner_name'] . $extension;
        $fileNameBn = $v['banner_name_web_bn'] . $extension;
        $model = "roaming-category";

        if (!empty($v['banner_web'])) {
            $bannerType = "banner-web";
            $data[$count]['banner_image_web_en'] = "/$bannerType/$model/$fileNameEn";
            $data[$count]['banner_image_web_bn'] = "/$bannerType/$model/$fileNameBn";
        }
        if (!empty($v['banner_mobile'])) {
            $bannerType = "banner-mobile";
            $data[$count]['banner_image_mobile_en'] = "/$bannerType/$model/$fileNameEn";
            $data[$count]['banner_image_mobile_bn'] = "/$bannerType/$model/$fileNameBn";
        }

        return $data;
    }
}
