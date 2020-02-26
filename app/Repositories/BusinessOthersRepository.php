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
        $servces = $this->model
                        ->where(
                                array(
                                    'status' => 1,
                                    'home_show' => 1
                                )
                        )
                        ->orderBy('sort')->get();

        $data = [];
        $countTop = 0;
        $countSlider = 0;
        foreach ($servces as $s) {
            if ($s->in_home_slider == 0) {
                $data['top'][$countTop]['id'] = $s->id;
                $data['top'][$countTop]['icon'] = config('filesystems.image_host_url') . $s->icon;
                $data['top'][$countTop]['name_en'] = $s->name;
                $data['top'][$countTop]['name_bn'] = $s->name_bn;
                $data['top'][$countTop]['short_details_en'] = $s->short_details;
                $data['top'][$countTop]['short_details_bn'] = $s->short_details_bn;
                $countTop++;
            } else {
                $data['slider'][$countSlider]['id'] = $s->id;
                $data['slider'][$countSlider]['banner_photo'] = config('filesystems.image_host_url') . $s->banner_photo;
                $data['slider'][$countSlider]['alt_text'] = $s->alt_text;
                $data['slider'][$countSlider]['icon'] = config('filesystems.image_host_url') . $s->icon;
                $data['slider'][$countSlider]['name_en'] = $s->name;
                $data['slider'][$countSlider]['name_bn'] = $s->name_bn;
                $data['slider'][$countSlider]['short_details_en'] = $s->short_details;
                $data['slider'][$countSlider]['short_details_bn'] = $s->short_details_bn;
                $countSlider++;
            }
        }
        return $data;
    }

    public function getOtherService($type) {
        $servces = $this->model->where('type', $type)->orderBy('sort')->get();

        $data = [];
        $count = 0;
        foreach ($servces as $s) {
            $data[$count]['id'] = $s->id;
            $data[$count]['banner_photo'] = config('filesystems.image_host_url') . $s->banner_photo;
            $data[$count]['alt_text'] = $s->alt_text;
            $data[$count]['icon'] = config('filesystems.image_host_url') . $s->icon;
            $data[$count]['name_en'] = $s->name;
            $data[$count]['name_bn'] = $s->name_bn;
            $data[$count]['short_details_en'] = $s->short_details;
            $data[$count]['short_details_bn'] = $s->short_details_bn;

            $count++;
        }
        return $data;
    }

    public function getServiceById($serviceId) {
        $service = $this->model->where('id', $serviceId)->first();
        
        $data['id'] = $service->id;
        $data['type'] = $service->type;
        $data['icon'] = config('filesystems.image_host_url') . $service->icon;
        $data['name_en'] = $service->name;
        $data['name_bn'] = $service->name_bn;
        $data['short_details_en'] = $service->short_details;
        $data['short_details_bn'] = $service->short_details_bn;
        $data['offer_details_en'] = $service->offer_details;
        $data['offer_details_bn'] = $service->offer_details_bn;
        
        return $data;
    }

}
