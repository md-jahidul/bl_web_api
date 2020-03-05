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
            $data[$count]['banner_photo'] = config('filesystems.image_host_url') . $p->banner_photo;
            $data[$count]['alt_text'] = $p->alt_text;
            $data[$count]['short_details_en'] = $p->short_details;
            $data[$count]['short_details_bn'] = $p->short_details_bn;

            $count++;
        }
        return $data;
    }

    public function getPackageById($packageId) {
        $package = $this->model->where('id', $packageId)->first();
        $data = [];
        if (!empty($package)) {
            $data['id'] = $package->id;
            $data['slug'] = 'packages';
            $data['name_en'] = $package->name;
            $data['name_bn'] = $package->name_bn;
            $data['short_details_en'] = $package->short_details;
            $data['short_details_bn'] = $package->short_details_bn;
            $data['main_details_en'] = $package->main_details;
            $data['main_details_bn'] = $package->main_details_bn;
            $data['offer_details_en'] = $package->offer_details;
            $data['offer_details_bn'] = $package->offer_details_bn;
        }
        return $data;
    }

}
