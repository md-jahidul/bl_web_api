<?php

/**
 * Dev: Bulbul Mahmud Nito
 * Date: 10/03/2020
 */

namespace App\Repositories;

use App\Models\SearchPopularKeywords;

class PopularSearchRepository extends BaseRepository {

    public $modelName = SearchPopularKeywords::class;

    public function getResults($limit) {
         $response = $this->model->select('keyword', 'keyword_bn', 'url as product_url', 'url_bn as product_url_bn')
                 ->offset(0)->limit($limit)
                 ->where('status', 1)->orderBy('sort')->get();
        return $response;
    }

}
