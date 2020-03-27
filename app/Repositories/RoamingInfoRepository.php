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
                $data[$key]['card_text_en'] = $v->card_text_en;
                $data[$key]['card_text_bn'] = $v->card_text_bn;
                $data[$key]['likes'] = $v->likes;
            }

        return $data;
    }
    
    public function getInfoDetails($infoId) {

        $info = $this->model->findOrFail($infoId);


        $data = [];
        
        $data['name_en'] = $info->name_en;
        $data['name_bn'] = $info->name_bn;
        $data['short_text_en'] = $info->short_text_en;
        $data['short_text_bn'] = $info->short_text_bn;
        $data['banner_web'] = $info->banner_web == "" ? "" : config('filesystems.image_host_url') . $info->banner_web;
        $data['banner_mobile'] = $info->banner_mobile == "" ? "" : config('filesystems.image_host_url') . $info->banner_mobile;
        $data['alt_text'] = $info->alt_text;
        $data['page_header'] = $info->page_header;
        $data['schema_markup'] = $info->schema_markup;
        $data['likes'] = $info->likes;

        $components = RoamingInfoComponents::where('parent_id', $infoId)->orderBy('position')->get();
        $data['components'] = [];
        foreach ($components as $k => $val) {
            
            $textEn = json_decode($val->body_text_en);
            $textBn = json_decode($val->body_text_bn);
            
            $data['components'][$k]['component_type'] = $val->component_type;
            $data['components'][$k]['data_en'] = $textEn;
            $data['components'][$k]['data_bn'] = $textBn;
         
        }

        return $data;
    }
    
    
    
 
    
   

}
