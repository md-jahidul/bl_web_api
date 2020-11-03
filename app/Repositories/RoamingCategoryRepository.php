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
            $data[$count]['page_header'] = $v->page_header;
            $data[$count]['page_header_bn'] = $v->page_header_bn;
            $data[$count]['schema_markup'] = $v->schema_markup;
            $data[$count]['name_en'] = $v->name_en;
            $data[$count]['name_bn'] = $v->name_bn;
            $data[$count]['alt_text'] = $v->alt_text;
            $data[$count]['banner_photo_web'] = $v->banner_web == "" ? "" : config('filesystems.image_host_url') . $v->banner_web;
            $data[$count]['banner_photo_mobile'] = $v->banner_mobile == "" ? "" : config('filesystems.image_host_url') . $v->banner_mobile;
            $count++;
        }
        return $data;
    }

}
