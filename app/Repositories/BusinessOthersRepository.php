<?php

/**
 * User: Bulbul Mahmud Nito
 * Date: 24/02/2020
 */

namespace App\Repositories;

use App\Models\BusinessOthers;

class BusinessOthersRepository extends BaseRepository {

    public $modelName = BusinessOthers::class;

    public function getHomeOtherService() {
        $services['servicesTop'] = $this->model
                        ->where(
                                array(
                                    'status' => 1,
                                    'home_show' => 1
                                )
                        )
                        ->orderBy('sort')->get();

        $services['servicesSlider'] = $this->model
            ->where(
                array(
                    'status' => 1,
                    'in_home_slider' => 1
                )
            )
            ->orderBy('sort')->get();

        return $services;

//        $data = [];
//        $countTop = 0;
//
//        foreach ($servcesTop as $s) {
//            $data['top'][$countTop]['id'] = $s->id;
//            $data['top'][$countTop]['slug'] = $s->type;
//            $data['top'][$countTop]['icon'] = $s->icon == "" ? "" : config('filesystems.image_host_url') . $s->icon;
//            $data['top'][$countTop]['name_en'] = $s->name;
//            $data['top'][$countTop]['name_bn'] = $s->name_bn;
//            $data['top'][$countTop]['home_short_details_en'] = $s->home_short_details_en;
//            $data['top'][$countTop]['home_short_details_bn'] = $s->home_short_details_bn;
//            $data['top'][$countTop]['short_details_en'] = $s->short_details;
//            $data['top'][$countTop]['short_details_bn'] = $s->short_details_bn;
//            $data['top'][$countTop]['url_slug'] = $s->url_slug;
//            $data['top'][$countTop]['url_slug_bn'] = $s->url_slug_bn;
//            $countTop++;
//        }
//
//
//        $countSlider = 0;
//        foreach ($servcesSlider as $s) {
//
//            $data['slider'][$countSlider]['id'] = $s->id;
//            $data['slider'][$countSlider]['slug'] = $s->type;
//            $data['slider'][$countSlider]['banner_photo'] = $s->banner_photo == "" ? "" : config('filesystems.image_host_url') . $s->banner_photo;
//            $data['slider'][$countSlider]['banner_photo_mobile'] = $s->banner_image_mobile == "" ? "" : config('filesystems.image_host_url') . $s->banner_image_mobile;
//            $data['slider'][$countSlider]['alt_text'] = $s->alt_text;
//            $data['slider'][$countSlider]['icon'] = config('filesystems.image_host_url') . $s->icon;
//            $data['slider'][$countSlider]['name_en'] = $s->name;
//            $data['slider'][$countSlider]['name_bn'] = $s->name_bn;
//            $data['slider'][$countSlider]['home_short_details_en'] = $s->home_short_details_en;
//            $data['slider'][$countSlider]['home_short_details_bn'] = $s->home_short_details_bn;
//            $data['slider'][$countSlider]['short_details_en'] = $s->short_details;
//            $data['slider'][$countSlider]['short_details_bn'] = $s->short_details_bn;
//            $data['slider'][$countSlider]['url_slug'] = $s->url_slug;
//            $data['slider'][$countSlider]['url_slug_bn'] = $s->url_slug_bn;
//            $countSlider++;
//        }
//        return $data;
    }

    public function getOtherService($type) {
        $servces = $this->model->where('type', $type)->where('status', 1)->orderBy('sort')->get();

        $data = [];
        $count = 0;
        foreach ($servces as $s) {
            $data[$count]['id'] = $s->id;
            $data[$count]['banner_photo'] = $s->banner_photo == "" ? "" : config('filesystems.image_host_url') . $s->banner_photo;
            $data[$count]['banner_photo_mobile'] = $s->banner_image_mobile == "" ? "" : config('filesystems.image_host_url') . $s->banner_image_mobile;
            $data[$count]['alt_text'] = $s->alt_text;
            $data[$count]['icon'] = $s->icon == "" ? "" : config('filesystems.image_host_url') . $s->icon;
            $data[$count]['name_en'] = $s->name;
            $data[$count]['name_bn'] = $s->name_bn;
            $data[$count]['short_details_en'] = $s->home_short_details_en;
            $data[$count]['short_details_bn'] = $s->home_short_details_bn;
            $data[$count]['page_header'] = $s->page_header;
            $data[$count]['page_header_bn'] = $s->page_header_bn;
            $data[$count]['schema_markup'] = $s->schema_markup;
            $data[$count]['url_slug'] = $s->url_slug;
            $data[$count]['url_slug_bn'] = $s->url_slug_bn;

            $count++;
        }
        return $data;
    }

    public function getServiceBySlug($serviceSlug) {
        $service = $this->model->where('url_slug', $serviceSlug)->orWhere('url_slug_bn', $serviceSlug)->first();

        $data['id'] = $service->id;
        $data['slug'] = $service->type;
        $data['icon'] = $service->icon == "" ? "" : config('filesystems.image_host_url') . $service->icon;
        $data['banner_photo'] =  $service->details_banner_web == "" ? "" : config('filesystems.image_host_url') . $service->details_banner_web;
        $data['banner_photo_mobile'] = $service->details_banner_mobile == "" ? "" : config('filesystems.image_host_url') . $service->details_banner_mobile;
        $data['alt_text'] = $service->details_alt_text;
        $data['name_en'] = $service->name;
        $data['name_bn'] = $service->name_bn;
        $data['short_details_en'] = $service->short_details;
        $data['short_details_bn'] = $service->short_details_bn;
        $data['offer_details_en'] = $service->offer_details_en;
        $data['offer_details_bn'] = $service->offer_details_bn;
        $data['url_slug'] = $service->url_slug;
        $data['url_slug_bn'] = $service->url_slug_bn;
        $data['page_header'] = $service->page_header;
        $data['page_header_bn'] = $service->page_header_bn;
        $data['schema_markup'] = $service->schema_markup;

        return $data;
    }

}
