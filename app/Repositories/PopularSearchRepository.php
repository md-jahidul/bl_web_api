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
         $response = $this->model->select('keyword', 'url as product_url')
                 ->offset(0)->limit($limit)
                 ->where('status', 1)->orderBy('sort')->get();
        return $response;
    }

}
