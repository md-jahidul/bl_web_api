<?php

/**
 * User: Bulbul Mahmud Nito
 * Date: 11/02/2020
 */

namespace App\Repositories;

use App\Models\BusinessInternet;
use App\Models\TagCategory;

class BusinessInternetRepository extends BaseRepository {

    public $modelName = BusinessInternet::class;

    public function getInternetPackageList($homeShow = 0) {

        $internet = $this->model->where('status', 1)
            ->whereNotNull('product_code')
            ->where('product_code', '!=', "")
            ->orderBy('sort');

        if ($homeShow == 1) {
            $internet->where('home_show', 1);
        }

        $packages = $internet->get();

        $data = [];
        $count = 0;
        foreach ($packages as $p) {

            $dataVol = $p->data_volume;
            if ($p->volume_data_unit == "GB") {
                $dataVol = $p->data_volume * 1024;
            }

            $data[$count]['id'] = $p->id;
            $data[$count]['type_en'] = $p->type;
            $data[$count]['type_bn'] = $p->type == 'Prepaid' ? 'প্রিপেইড' : 'পোস্টপেইড';;
            $data[$count]['product_code'] = $p->product_code;
            $data[$count]['internet_volume_mb'] = $dataVol;
            $data[$count]['volume_data_unit'] = $p->volume_data_unit;
            $data[$count]['validity_days'] = $p->validity;
            $data[$count]['validity_unit'] = $p->validity_unit;
            $data[$count]['price_tk'] = $p->mrp;
            $data[$count]['tag_category_id'] = $p->tag_id;
            $data[$count]['ussd_en'] = $p->activation_ussd_code;
            $data[$count]['balance_check_ussd_code'] = $p->balance_check_ussd_code;
            $data[$count]['page_header'] = $p->page_header;
            $data[$count]['page_header_bn'] = $p->page_header_bn;
            $data[$count]['schema_markup'] = $p->schema_markup;
            $data[$count]['url_slug'] = $p->url_slug;
            $data[$count]['url_slug_bn'] = $p->url_slug_bn;
            $data[$count]['likes'] = $p->likes;
            $count++;
        }

        return $data;
    }

    public function getInternetPackageDetails($internetSlug) {

        $internet = $this->model->where('url_slug', $internetSlug)->orWhere('url_slug_bn', $internetSlug)->first();

        $relatedProduct = $this->model->whereIn("id", array($internet->related_product))->get();

        $product = [
            'internet' => $internet,
            'relatedProduct' => $relatedProduct
        ];

        return $product;
    }

    public function internetLike($internetId) {

        $internet = $this->model->findOrFail($internetId);
        $likes = $internet->likes + 1;
        $internet->likes = $likes;
        $internet->save();
        $data['likes'] = $likes;
        return $data;
    }

}
