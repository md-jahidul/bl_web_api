<?php

/**
 * User: Bulbul Mahmud Nito
 * Date: 26/02/2020
 */

namespace App\Repositories;

use App\Models\RoamingInfo;
use App\Models\RoamingInfoComponents;

class RoamingInfoRepository extends BaseRepository {

    public $modelName = RoamingInfo::class;

    public function getInfoTips() {


            $infos = $this->model->where('status', 1)->orderBy('id', 'desc')->get();

            $data = [];
            foreach ($infos as $key => $v) {
                $data[$key]['id'] = $v->id;
                $data[$key]['name_en'] = $v->name_en;
                $data[$key]['name_bn'] = $v->name_bn;
                $data[$key]['url_slug'] = $v->url_slug;
                $data[$key]['url_slug_bn'] = $v->url_slug_bn;
                $data[$key]['page_header'] = $v->page_header;
                $data[$key]['page_header_bn'] = $v->page_header_bn;
                $data[$key]['schema_markup'] = $v->schema_markup;
                $data[$key]['card_text_en'] = $v->card_text_en;
                $data[$key]['card_text_bn'] = $v->card_text_bn;
                $data[$key]['likes'] = $v->likes;
            }

        return $data;
    }

    public function getInfoDetails($infoSlug)
    {
        $info = $this->model->where('url_slug', $infoSlug)->orWhere('url_slug_bn', $infoSlug)->first();

        return $info;
    }
}
