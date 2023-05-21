<?php

/**
 * Dev: Bulbul Mahmud Nito
 * Date: 10/03/2020
 */

namespace App\Repositories;

use App\Models\SearchableData;
use App\Models\SearchData;
use App\Models\SearchSettings;

class SearchRepository extends BaseRepository {

    public $modelName = SearchableData::class;

    public function searchSuggestion($keyword) {
        $keywords = $keyword;

        $kwArray = explode(' ', $keyword);
        $keywords .= ", " . implode(', ', $kwArray);
        $keywords .= ", " . $keyword . "*";
        $keywords .= ", " . implode('*, ', $kwArray) . "*";
        return $this->model->select('product_code', 'page_title_en', 'page_title_bn', 'url_slug_en', 'url_slug_bn')
                        ->whereRaw("MATCH(page_title_en,page_title_bn,tag_en,tag_bn) AGAINST('$keywords' IN BOOLEAN MODE)")
                        ->where('status', 1)->get();
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
