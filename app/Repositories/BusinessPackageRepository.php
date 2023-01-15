<?php

/**
 * User: Bulbul Mahmud Nito
 * Date: 11/02/2020
 */

namespace App\Repositories;

use App\Models\BusinessPackages;

class BusinessPackageRepository extends BaseRepository {

    public $modelName = BusinessPackages::class;

    public function getPackageList($homeShow = 0) {
        $packages = $this->model->orderBy('sort')->where('status', 1);
        if ($homeShow == 1) {
            $packages->where('home_show', $homeShow);
        }

        $packageData = $packages->get();

        $data = [];
        $count = 0;
        foreach ($packageData as $p) {

            $data[$count]['id'] = $p->id;
            $data[$count]['slug'] = 'packages';
            $data[$count]['name_en'] = $p->name;
            $data[$count]['name_bn'] = $p->name_bn;
            $data[$count]['banner_photo'] = $p->card_banner_web == "" ? "" :  $p->card_banner_web;
            $data[$count]['banner_photo_mobile'] = $p->card_banner_mobile == "" ? "" :  $p->card_banner_mobile;
            $data[$count]['alt_text'] = $p->card_banner_alt_text;
            $data[$count]['short_details_en'] = $p->short_details;
            $data[$count]['short_details_bn'] = $p->short_details_bn;
            $data[$count]['page_header'] = $p->page_header;
            $data[$count]['page_header_bn'] = $p->page_header_bn;
            $data[$count]['schema_markup'] = $p->schema_markup;
            $data[$count]['url_slug'] = $p->url_slug;
            $data[$count]['url_slug_bn'] = $p->url_slug_bn;

            $count++;
        }
        return $data;
    }

    public function getPackageById($packageSlug) {
        $package = $this->model->where('url_slug', $packageSlug)->orWhere('url_slug_bn', $packageSlug)->first();
        $data = [];
        if (!empty($package)) {
            $data['id'] = $package->id;
            $data['slug'] = 'packages';
            $data['name_en'] = $package->name;
            $data['name_bn'] = $package->name_bn;
            $data['banner_photo'] = $package->banner_photo == "" ? "" :  $package->banner_photo;
            $data['banner_photo_mobile'] = $package->banner_image_mobile == "" ? "" :  $package->banner_image_mobile;
            $data['alt_text'] = $package->alt_text;
            $data['short_details_en'] = $package->short_details;
            $data['short_details_bn'] = $package->short_details_bn;
            $data['main_details_en'] = $package->main_details;
            $data['main_details_bn'] = $package->main_details_bn;
            $data['offer_details_en'] = $package->offer_details;
            $data['offer_details_bn'] = $package->offer_details_bn;
            $data['url_slug'] = $package->url_slug;
            $data['url_slug_bn'] = $package->url_slug_bn;
            $data['page_header'] = $package->page_header;
            $data['page_header_bn'] = $package->page_header_bn;
            $data['schema_markup'] = $package->schema_markup;
        }
        return $data;
    }

}
