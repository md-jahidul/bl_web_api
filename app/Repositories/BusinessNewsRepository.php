<?php

/**
 * User: Bulbul Mahmud Nito
 * Date: 24/02/2020
 */

namespace App\Repositories;

use App\Models\BusinessNews;

class BusinessNewsRepository extends BaseRepository {

    public $modelName = BusinessNews::class;

    public function getNews() {
        $data = [];
        $news = $this->model->where('status', 1)->orderBy('sort')->get();
        $count = 0;
        foreach($news as $v){
            $data[$count]['image'] = config('filesystems.image_host_url'). $v->image_url;
            $data[$count]['sliding_speed'] = $v->sliding_speed;
            $data[$count]['alt_text'] = $v->alt_text;
            $data[$count]['title'] = $v->title;
            $data[$count]['title_bn'] = $v->title_bn;
            $data[$count]['body'] = $v->body;
            $data[$count]['body_bn'] = $v->body_bn;
            $count++;
        }
        return $data;
    }
   
}
