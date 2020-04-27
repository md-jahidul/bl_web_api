<?php

/**
 * Dev: Bulbul Mahmud Nito
 * Date: 10/03/2020
 */

namespace App\Repositories;

use App\Models\SearchData;
use App\Models\SearchSettings;

class SearchRepository extends BaseRepository {

    public $modelName = SearchData::class;

    public function searchSuggestion($keyword) {
        $keywrods = $keyword;

        $kwArray = explode(' ', $keyword);

        $keywrods .= ", " . implode(', ', $kwArray);

        $keywrods .= ", " . $keyword . "*";
        $keywrods .= ", " . implode('*, ', $kwArray) . "*";



        $response = $this->model->select('keyword', 'url as product_url', 'type')
                        ->where('status', 1)
                        ->whereRaw("MATCH(keyword,tag) AGAINST('$keywrods' IN BOOLEAN MODE)")->get();
        return $response;
    }

    public function searchData($keyword) {

        $keywrods = $keyword;

        $kwArray = explode(' ', $keyword);

        $keywrods .= ", " . implode(', ', $kwArray);

        $keywrods .= ", " . $keyword . "*";
        $keywrods .= ", " . implode('*, ', $kwArray) . "*";

        $response = $this->model->select('keyword', 'url as product_url', 'type')
                        ->where('status', 1)
                        ->whereRaw("MATCH(keyword,tag) AGAINST('$keywrods' "
                                . "IN BOOLEAN MODE)")->get();
        return $response;
    }

    public function getSettingData() {
        $settings = SearchSettings::select('type_slug', 'limit')->get();

        $data = [];
        foreach ($settings as $val) {
            $data[$val->type_slug] = $val->limit;
        }
        return $data;
    }

}
