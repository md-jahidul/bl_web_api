<?php


/**
 * Dev: Bulbul Mahmud Nito
 * Date: 10/03/2020
 */

namespace App\Repositories;

use App\Models\SearchData;

class SearchRepository extends BaseRepository {

     public $modelName = SearchData::class;
     
     public function searchSuggestion($keyword) {
         $response = $this->model->select('product_name as keyword', 'url as product_url', 'type')
                 ->where('status', 1)->where('product_name', 'like', '%'.$keyword.'%')
                 ->orWhere('tag', 'like', '%'.$keyword.'%')->get();
        return $response;
    }
     public function searchData($keyword) {
         $response = $this->model->select('product_name as keyword', 'url as product_url', 'type')
                 ->where('status', 1)->where('product_name', 'like', '%'.$keyword.'%')
                 ->orWhere('tag', 'like', '%'.$keyword.'%')->get();
        return $response;
    }
   
}
