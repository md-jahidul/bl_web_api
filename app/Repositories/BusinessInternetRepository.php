<?php

/**
 * User: Bulbul Mahmud Nito
 * Date: 11/02/2020
 */

namespace App\Repositories;

use App\Models\BusinessInternet;

class BusinessInternetRepository extends BaseRepository {

    public $modelName = BusinessInternet::class;

    public function getInternetPackageList($homeShow = 0) {

        $internet = $this->model->where('status', 1)->orderBy('sort');
        if($homeShow == 1){
            $internet->where('home_show', 1);
        }
        
        $packages = $internet->get();
        
        $data = [];
        $count = 0;
        foreach($packages as $p){
            $data[$count]['id'] = $p->id;
            $data[$count]['data_volume'] = $p->data_volume;
            $data[$count]['volume_data_unit'] = $p->volume_data_unit;
            $data[$count]['validity'] = $p->validity;
            $data[$count]['validity_unit'] = $p->validity_unit;
            $data[$count]['price_tk'] = $p->mrp;
            $data[$count]['tag_en'] = "Best Offer";
            $data[$count]['tag_bn'] = "সেরা অফার";
            $data[$count]['tag_color'] = "#21874d";
            $data[$count]['activation_ussd_code'] = $p->activation_ussd_code;
            $count++;
        }
        
        return $data;


    }
    

}
