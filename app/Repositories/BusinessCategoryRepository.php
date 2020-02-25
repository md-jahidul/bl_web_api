<?php

/**
 * User: Bulbul Mahmud Nito
 * Date: 24/02/2020
 */

namespace App\Repositories;

use App\Models\BusinessCategory;

class BusinessCategoryRepository extends BaseRepository {

    public $modelName = BusinessCategory::class;

    public function getHomeCategoryList() {
        $categories = $this->model
                ->where('home_show', 1)
                ->orderBy('home_sort')->get();
        $data = [];
        $count = 0;
        
        $slugs = array(
            1 => 'packages',
            2 => 'internet',
            3 => 'business-solutions',
            4 => 'iot',
            5 => 'others',
        );
        foreach($categories as $v){
            $data[$count]['id'] = $v->id;
             $data[$count]['slug'] = $slugs[$v->id];
            $data[$count]['name_en'] = $v->name;
            $data[$count]['name_bn'] = $v->name_bn;
            $count++;
        }
        return $data;
    }

    public function getCategoryList() {
        $categories = $this->model
                ->orderBy('id')->get();
        $data = [];
        $count = 0;
        
        $slugs = array(
            1 => 'packages',
            2 => 'internet',
            3 => 'business-solutions',
            4 => 'iot',
            5 => 'others',
        );
        foreach($categories as $v){
            $data[$count]['id'] = $v->id;
            $data[$count]['slug'] = $slugs[$v->id];
            $data[$count]['name_en'] = $v->name;
            $data[$count]['name_bn'] = $v->name_bn;
            $count++;
        }
        return $data;
    }

}
