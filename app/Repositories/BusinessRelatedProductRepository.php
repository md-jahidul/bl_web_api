<?php

/**
 * User: Bulbul Mahmud Nito
 * Date: 01/03/2020
 */

namespace App\Repositories;

use App\Models\BusinessRelatedProducts;

class BusinessRelatedProductRepository extends BaseRepository {

    public $modelName = BusinessRelatedProducts::class;

    public function getPackageRelatedProduct($parentId, $productType) {
        $data = $this->model->select('p.id', 'p.name', 'p.name_bn', 'p.banner_photo', 'p.banner_image_mobile', 'p.url_slug', 'p.alt_text', 'p.short_details', 'p.short_details_bn')
                        ->leftJoin('business_packages as p', 'p.id', '=', 'business_related_products.product_id')
                        ->where(array(
                            'product_type' => $productType,
                            'parent_id' => $parentId
                        ))->get();

        $related = [];
        $count = 0;
        foreach ($data as $p) {
            $related[$count]['package_id'] = $p->id;
            $related[$count]['slug'] = 'packages';
            $related[$count]['name_en'] = $p->name;
            $related[$count]['name_bn'] = $p->name_bn;
            $related[$count]['banner_photo'] = $p->banner_photo == "" ? "" : config('filesystems.image_host_url') . $p->banner_photo;
            $related[$count]['banner_photo_mobile'] = $p->banner_image_mobile == "" ? "" : config('filesystems.image_host_url') . $p->banner_image_mobile;
            $related[$count]['alt_text'] = $p->alt_text;
            $related[$count]['short_details_en'] = $p->short_details;
            $related[$count]['short_details_bn'] = $p->short_details_bn;
            $related[$count]['url_slug'] = $p->url_slug;

            $count++;
        }
        return $related;
    }

    public function getEnterpriseRelatedProduct($parentId, $productType) {
        $data = $this->model->select('s.id', 's.name', 's.name_bn', 's.icon', 's.url_slug', 's.short_details', 's.short_details_bn', 's.type')
                        ->leftJoin('business_other_services as s', 's.id', '=', 'business_related_products.product_id')
                        ->where(array(
                            'product_type' => $productType,
                            'parent_id' => $parentId
                        ))->get();

        $related = [];
        $count = 0;
        foreach ($data as $p) {
            $related[$count]['package_id'] = $p->id;
            $related[$count]['slug'] = $p->type;
            $related[$count]['name_en'] = $p->name;
            $related[$count]['name_bn'] = $p->name_bn;
            $related[$count]['icon'] = $p->icon == "" ? "" : config('filesystems.image_host_url') . $p->icon;
            $related[$count]['short_details_en'] = $p->short_details;
            $related[$count]['short_details_bn'] = $p->short_details_bn;
            $related[$count]['url_slug'] = $p->url_slug;

            $count++;
        }
        return $related;
    }

}
