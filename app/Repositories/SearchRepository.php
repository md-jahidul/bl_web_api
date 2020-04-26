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
        $limits = $this->getSettingData();

        $keywrods = $keyword;

        $kwArray = explode(' ', $keyword);

        $keywrods .= "," . implode(',', $kwArray);
        $keywrods .= "," . implode('', $kwArray);
        $keywrods .= "," . implode('-', $kwArray);

        foreach ($kwArray as $k => $v) {
            if (($k + 1) < count($kwArray)) {
                $keywrods .= "," . $v . "" . $kwArray[$k + 1];
                $keywrods .= "," . $v . " " . $kwArray[$k + 1];
                $keywrods .= "," . $v . "-" . $kwArray[$k + 1];
            }
        }

        $preIntLimit = $limits['prepaid-internet'];
        $preInternet = $this->model->select('keyword', 'url as product_url', 'type')
                        ->where('status', 1)->where('type', 'prepaid-internet')
                        ->whereRaw("MATCH(keyword,tag) AGAINST('$keywrods' IN NATURAL LANGUAGE MODE)")
                        ->offset(0)->limit($preIntLimit)->get();

        $preVoiceLimit = $limits['prepaid-voice'];
        $preVoice = $this->model->select('keyword', 'url as product_url', 'type')
                        ->where('status', 1)->where('type', 'prepaid-voice')
                        ->whereRaw("MATCH(keyword,tag) AGAINST('$keywrods' IN NATURAL LANGUAGE MODE)")
                        ->offset(0)->limit($preVoiceLimit)->get();

        $preBundleLimit = $limits['prepaid-bundle'];
        $preBundle = $this->model->select('keyword', 'url as product_url', 'type')
                        ->where('status', 1)->where('type', 'prepaid-bundle')
                        ->whereRaw("MATCH(keyword,tag) AGAINST('$keywrods' IN NATURAL LANGUAGE MODE)")
                        ->offset(0)->limit($preBundleLimit)->get();

        $postIntLimit = $limits['postpaid-internet'];
        $postInt = $this->model->select('keyword', 'url as product_url', 'type')
                        ->where('status', 1)->where('type', 'postpaid-internet')
                        ->whereRaw("MATCH(keyword,tag) AGAINST('$keywrods' IN NATURAL LANGUAGE MODE)")
                        ->offset(0)->limit($postIntLimit)->get();

        $otherLimit = $limits['others'];
        $others = $this->model->select('keyword', 'url as product_url', 'type')
                        ->where('status', 1)->where('type', 'others')
                        ->whereRaw("MATCH(keyword,tag) AGAINST('$keywrods' IN NATURAL LANGUAGE MODE)")
                        ->offset(0)->limit($otherLimit)->get();

        $resopnse = array($preInternet, $preVoice, $preBundle, $postInt, $others);
        return $resopnse;
    }

    public function searchData($keyword) {

        $keywrods = $keyword."*";

        $kwArray = explode(' ', $keyword);

        $keywrods .= "," . implode('*,', $kwArray)."*";
        $keywrods .= "," . implode('', $kwArray)."*";
        $keywrods .= "," . implode('-', $kwArray)."*";

        foreach ($kwArray as $k => $v) {
            if (($k + 1) < count($kwArray)) {
                $keywrods .= "," . $v . "" . $kwArray[$k + 1]."*";
                $keywrods .= "," . $v . "* " . $kwArray[$k + 1]."*";
                $keywrods .= "," . $v . "-" . $kwArray[$k + 1]."*";
            }
        }
        
        echo $keywrods;
        die();


//        dd($keywrods);
        $response = $this->model->select('keyword', 'url as product_url', 'type')
                        ->selectRaw("MATCH(keyword,tag) AGAINST('$keywrods' IN BOOLEAN MODE) as score")
                        ->where('status', 1)
                        ->whereRaw("MATCH(keyword,tag) AGAINST('$keywrods' "
                                . "IN BOOLEAN MODE)")->orderBy('score', 'desc')->get();
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
