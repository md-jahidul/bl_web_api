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
    }

    public function getOtherService($type)
    {
        $services = $this->model->select('id',
                    'banner_photo',
                    'banner_image_mobile',
                    'banner_name', 'banner_name_bn',
                    'alt_text', 'alt_text_bn',
                    'icon', 'name', 'name_bn',
                    'home_short_details_en', 'home_short_details_bn',
                    'page_header', 'page_header_bn', 'schema_markup',
                    'url_slug', 'url_slug_bn')->where('type', $type)->where('status', 1)->orderBy('sort')->get();

        return $services;
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
