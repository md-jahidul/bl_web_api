<?php

/**
 * User: Bulbul Mahmud Nito
 * Date: 24/06/2020
 */

namespace App\Repositories;

use App\Models\EthicsInfo;
use App\Models\EthicsFiles;

class EthicsRepository extends BaseRepository {

    public $modelName = EthicsInfo::class;

    public function getPageInfo() {
        $info = $this->model->first();
        $data = [];
        $data['page_name_en'] = $info->page_name_en;
        $data['page_name_bn'] = $info->page_name_bn;
        $data['banner_web'] = $info->banner_web  == "" ? "" : config('filesystems.image_host_url') . $info->banner_web;
        $data['banner_mobile'] = $info->banner_mobile == "" ? "" : config('filesystems.image_host_url') . $info->banner_mobile;
        $data['alt_text'] = $info->alt_text;
        $data['page_header'] = $info->page_header;
        $data['page_header_bn'] = $info->page_header_bn;
        $data['schema_markup'] = $info->schema_markup;

        return $data;
    }

    public function getFiles() {
        $files = EthicsFiles::where('status', 1)->where('file_path', '!=', NULL)
                        ->orderBy('sort')->get();
        $data = [];
        $count = 0;

        foreach ($files as $v) {
            $data[$count]['file_name_en'] = $v->file_name_en;
            $data[$count]['file_name_bn'] = $v->file_name_bn;
            $data[$count]['title_en'] = $v->title_en;
            $data[$count]['title_bn'] = $v->title_bn;
            $data[$count]['description_en'] = $v->description_en;
            $data[$count]['description_bn'] = $v->description_bn;
            $data[$count]['image_url'] = $v->image_url;
            $data[$count]['mobile_view_img'] = $v->mobile_view_img;
            $data[$count]['image_name_en'] = $v->image_name_en;
            $data[$count]['image_name_bn'] = $v->image_name_bn;
            $data[$count]['alt_text'] = $v->alt_text;
            $data[$count]['alt_text_bn'] = $v->alt_text_bn;
            $data[$count]['file_path'] = $v->file_path;
            $count++;
        }
        return $data;
    }

}
