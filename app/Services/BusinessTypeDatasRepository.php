<?php

/**
 * User: Md Fahreyad Hossain
 * Date: 12/12/2022
 */

namespace App\Repositories;

use App\Models\BusinessTypeData;

class BusinessTypeDatasRepository extends BaseRepository {

    public $modelName = BusinessTypeData::class;

    public function getBusinessTypeDataList($homeShow = 0) {
        $data = $this->model->where('status', 1)->get();
    //     if ($homeShow == 1) {
    //         $packages->where('home_show', $homeShow);
    //     }
        return $data;
    }

    // public function getPackageById($packageSlug) {
    //     $package = $this->model->where('url_slug', $packageSlug)->orWhere('url_slug_bn', $packageSlug)->first();
    //     $data = [];
    //     if (!empty($package)) {
    //         $data['id'] = $package->id;
    //         $data['slug'] = 'packages';
    //         $data['name_en'] = $package->name;
    //         $data['name_bn'] = $package->name_bn;
    //         $data['banner_photo'] = $package->banner_photo == "" ? "" : config('filesystems.image_host_url') . $package->banner_photo;
    //         $data['banner_photo_mobile'] = $package->banner_image_mobile == "" ? "" : config('filesystems.image_host_url') . $package->banner_image_mobile;
    //         $data['alt_text'] = $package->alt_text;
    //         $data['short_details_en'] = $package->short_details;
    //         $data['short_details_bn'] = $package->short_details_bn;
    //         $data['main_details_en'] = $package->main_details;
    //         $data['main_details_bn'] = $package->main_details_bn;
    //         $data['offer_details_en'] = $package->offer_details;
    //         $data['offer_details_bn'] = $package->offer_details_bn;
    //         $data['url_slug'] = $package->url_slug;
    //         $data['url_slug_bn'] = $package->url_slug_bn;
    //         $data['page_header'] = $package->page_header;
    //         $data['page_header_bn'] = $package->page_header_bn;
    //         $data['schema_markup'] = $package->schema_markup;
    //     }
    //     return $data;
    // }

}
