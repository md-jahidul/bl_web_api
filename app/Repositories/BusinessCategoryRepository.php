<?php

/**
 * User: Bulbul Mahmud Nito
 * Date: 24/02/2020
 */

namespace App\Repositories;

use App\Models\BusinessCategory;

class BusinessCategoryRepository extends BaseRepository {

    public $modelName = BusinessCategory::class;

    public function getHomeCategoryList() {
        $categories = $this->model
                        ->where('home_show', 1)
                        ->orderBy('home_sort')->get();
        $data = [];
        $count = 0;

        $slugs = array(
            1 => 'packages',
            2 => 'internet',
            3 => 'business-solution',
            4 => 'iot',
            5 => 'others',
        );
        foreach ($categories as $v) {
            $data[$count]['id'] = $v->id;
            $data[$count]['slug'] = $slugs[$v->id];
            $data[$count]['name_en'] = $v->name;
            $data[$count]['name_bn'] = $v->name_bn;
            $data[$count]['alt_text'] = $v->alt_text;
            $data[$count]['banner_photo'] = $v->banner_photo == "" ? "" : config('filesystems.image_host_url') . $v->banner_photo;
            $data[$count]['banner_photo_mobile'] = $v->banner_image_mobile == "" ? "" : config('filesystems.image_host_url') . $v->banner_image_mobile;
            $data[$count]['url_slug'] = $v->url_slug;
            $data[$count]['url_slug_bn'] = $v->url_slug_bn;
            $data[$count]['page_header'] = $v->page_header;
            $data[$count]['page_header_bn'] = $v->page_header_bn;
            $data[$count]['schema_markup'] = $v->schema_markup;

            $count++;
        }
        return $data;
    }

    public function getCategoryList() {
        $categories = $this->model->where('home_show', 1)
                        ->orderBy('id')->get();
        $data = [];
        $count = 0;

        $slugs = array(
            1 => 'packages',
            2 => 'internet',
            3 => 'business-solution',
            4 => 'iot',
            5 => 'others',
        );
        foreach ($categories as $v) {
            $data[$count]['id'] = $v->id;
            $data[$count]['slug'] = $slugs[$v->id];
            $data[$count]['name_en'] = $v->name;
            $data[$count]['name_bn'] = $v->name_bn;
            $data[$count]['alt_text'] = $v->alt_text;
            $data[$count]['banner_photo'] = $v->banner_photo == "" ? "" : config('filesystems.image_host_url') . $v->banner_photo;
            $data[$count]['banner_photo_mobile'] = $v->banner_image_mobile == "" ? "" : config('filesystems.image_host_url') . $v->banner_image_mobile;
            $data[$count]['url_slug'] = $v->url_slug;
            $data[$count]['url_slug_bn'] = $v->url_slug_bn;
            $data[$count]['page_header'] = $v->page_header;
            $data[$count]['page_header_bn'] = $v->page_header_bn;
            $data[$count]['schema_markup'] = $v->schema_markup;
            $count++;
        }
        return $data;
    }

}
