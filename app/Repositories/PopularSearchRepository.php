<?php

/**
 * Dev: Bulbul Mahmud Nito
 * Date: 10/03/2020
 */

namespace App\Repositories;

use App\Models\SearchPopularKeywords;

class PopularSearchRepository extends BaseRepository {

    public $modelName = SearchPopularKeywords::class;

    public function getResults() {
         $response = $this->model->select('keyword', 'url as product_url')->where('status', 1)->orderBy('sort')->get();
        return $response;
    }

}
