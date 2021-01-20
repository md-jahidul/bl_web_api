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
        $service = $this->model->select('id', 'type',
                                    'icon', 'details_banner_web', 'details_banner_name', 'details_banner_name_bn',
                                    'details_banner_mobile', 'details_alt_text', 'details_alt_text_bn',
                                    'name', 'name_bn', 'short_details', 'short_details_bn', 'offer_details_en',
                                    'offer_details_bn', 'url_slug', 'url_slug_bn', 'page_header', 'page_header_bn', 'schema_markup')
                                    ->where('url_slug', $serviceSlug)->orWhere('url_slug_bn', $serviceSlug)->first();

        return $service;

    }

}
