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

        $news = $this->model->where('status', 1)
                            ->select('sliding_speed',
                                    'title',
                                    'title_bn',
                                    'body',
                                    'body_bn',
                                    'image_url',
                                    'image_name_en',
                                    'image_name_bn',
                                    'alt_text',
                                    'alt_text_bn')
                            ->orderBy('sort')->get();
        return $news;
//        $data = [];
//        $count = 0;
//        foreach($news as $v){
//            $data['sliding_speed'] = $v->sliding_speed;
//            $data['data'][$count]['image'] = $v->image_url == "" ? "" : config('filesystems.image_host_url'). $v->image_url;
//            $data['data'][$count]['alt_text'] = $v->alt_text;
//            $data['data'][$count]['title'] = $v->title;
//            $data['data'][$count]['title_bn'] = $v->title_bn;
//            $data['data'][$count]['body'] = $v->body;
//            $data['data'][$count]['body_bn'] = $v->body_bn;
//            $count++;
//        }
//        return $data;
    }

}
