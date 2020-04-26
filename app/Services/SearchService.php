<?php

/**
 * Dev: Bulbul Mahmud Nito
 * Date: 10/03/2020
 */

namespace App\Services;

use App\Repositories\PopularSearchRepository;
use App\Repositories\SearchRepository;
use App\Services\Banglalink\BaseService;

class SearchService extends BaseService {

    /**
     * @var $popularRepository
     * @var $searchRepository
     */
    protected $popularRepository;
    protected $searchRepository;

    /**
     * @var ApiBaseService
     */
    protected $apiBaseService;

    /**
     * SearchService constructor.
     * @param PopularSearchRepository $popularRepository
     * @param SearchRepository $searchRepository
     */
    public function __construct(SearchRepository $searchRepository, PopularSearchRepository $popularRepository, ApiBaseService $apiBaseService) {
        $this->popularRepository = $popularRepository;
        $this->searchRepository = $searchRepository;
        $this->apiBaseService = $apiBaseService;
    }

    private function _getLimits() {
        $limit = $this->searchRepository->getSettingData();

        $data = [];
        foreach ($limit as $val) {
            $data[$val->type_slug] = $val->limit;
        }
        return $data;
    }

    public function popularSearch() {
        $limits = $this->searchRepository->getSettingData();
        $keywords = $this->popularRepository->getResults($limits['popular-search']);
        return $this->apiBaseService->sendSuccessResponse($keywords, 'Popular Search Result');
    }

    public function searchSuggestion($keyword) {

        $keywords = $this->searchRepository->searchSuggestion($keyword);

        $limits = $this->searchRepository->getSettingData();

        $data = [];

        $heads = array(
            'prepaid-internet' => "Prepid Internet",
            'prepaid-voice' => "Prepid Voice",
            'prepaid-bundle' => "Prepid Bundle",
            'postpaid-internet' => "Postpaid Internet",
            'others' => "Others"
        );

        $countPi = 0;
        $countPv = 0;
        $countPb = 0;
        $countPstI = 0;
        $countOth = 0;
        foreach ($keywords as $k) {
            if ($k->type != "") {

                if ($k->type == "prepaid-internet") {

                    if ($limits['prepaid-internet'] > $countPi) {
                        $data[$k->type]['head'] = $heads[$k->type];
                        $data[$k->type]['keywords'][$countPi]['keyword'] = $k->keyword;
                        $data[$k->type]['keywords'][$countPi]['product_url'] = $k->product_url;
                        
                    }
                    $countPi++;
                }

                if ($k->type == "prepaid-voice") {

                    if ($limits['prepaid-voice'] > $countPv) {
                        $data[$k->type]['head'] = $heads[$k->type];
                        $data[$k->type]['keywords'][$countPv]['keyword'] = $k->keyword;
                        $data[$k->type]['keywords'][$countPv]['product_url'] = $k->product_url;
                       
                    }
                     $countPv++;
                }

                if ($k->type == "prepaid-bundle") {

                    if ($limits['prepaid-bundle'] > $countPb) {
                        $data[$k->type]['head'] = $heads[$k->type];
                        $data[$k->type]['keywords'][$countPb]['keyword'] = $k->keyword;
                        $data[$k->type]['keywords'][$countPb]['product_url'] = $k->product_url;
                        
                    }
                    $countPb++;
                }

                if ($k->type == "postpaid-internet") {

                    if ($limits['postpaid-internet'] > $countPstI) {
                        $data[$k->type]['head'] = $heads[$k->type];
                        $data[$k->type]['keywords'][$countPstI]['keyword'] = $k->keyword;
                        $data[$k->type]['keywords'][$countPstI]['product_url'] = $k->product_url;
                       
                    }
                     $countPstI++;
                }

                if ($k->type == "others") {
                    if ($limits['others'] > $countOth) {
                        $data[$k->type]['head'] = $heads[$k->type];
                        $data[$k->type]['keywords'][$countOth]['keyword'] = $k->keyword;
                        $data[$k->type]['keywords'][$countOth]['product_url'] = $k->product_url;
                        
                    }
                    $countOth++;
                }
            }
        }
        
        $data['more_result'] = 0;
        if($countPi > $limits['prepaid-internet'] || $countPv > $limits['prepaid-voice'] || $countPb > $limits['prepaid-bundle'] || $countPstI > $limits['postpaid-internet'] || $countOth > $limits['others']){
            $data['more_result'] = 1;
        }
        
        
        
        return $this->apiBaseService->sendSuccessResponse($data, 'Search Suggestion');
    }

    public function searchData($keyword) {
        $keywords = $this->searchRepository->searchData($keyword);

        $data = [];

        $heads = array(
            'prepaid-internet' => "Prepid Internet",
            'prepaid-voice' => "Prepid Voice",
            'prepaid-bundle' => "Prepid Bundle",
            'postpaid-internet' => "Postpaid Internet",
            'others' => "Others"
        );
        $countPi = 0;
        $countPv = 0;
        $countPb = 0;
        $countPstI = 0;
        $countOth = 0;
        foreach ($keywords as $k) {
            if ($k->type != "") {

                if ($k->type == "prepaid-internet") {
                    $data[$k->type]['head'] = $heads[$k->type];
                    $data[$k->type]['keywords'][$countPi]['keyword'] = $k->keyword;
                    $data[$k->type]['keywords'][$countPi]['product_url'] = $k->product_url;
                    $countPi++;
                }

                if ($k->type == "prepaid-voice") {
                    $data[$k->type]['head'] = $heads[$k->type];
                    $data[$k->type]['keywords'][$countPv]['keyword'] = $k->keyword;
                    $data[$k->type]['keywords'][$countPv]['product_url'] = $k->product_url;
                    $countPv++;
                }

                if ($k->type == "prepaid-bundle") {
                    $data[$k->type]['head'] = $heads[$k->type];
                    $data[$k->type]['keywords'][$countPb]['keyword'] = $k->keyword;
                    $data[$k->type]['keywords'][$countPb]['product_url'] = $k->product_url;
                    $countPb++;
                }

                if ($k->type == "postpaid-internet") {
                    $data[$k->type]['head'] = $heads[$k->type];
                    $data[$k->type]['keywords'][$countPstI]['keyword'] = $k->keyword;
                    $data[$k->type]['keywords'][$countPstI]['product_url'] = $k->product_url;
                    $countPstI++;
                }

                if ($k->type == "others") {
                    $data[$k->type]['head'] = $heads[$k->type];
                    $data[$k->type]['keywords'][$countOth]['keyword'] = $k->keyword;
                    $data[$k->type]['keywords'][$countOth]['product_url'] = $k->product_url;
                    $countOth++;
                }
            }
        }
        return $this->apiBaseService->sendSuccessResponse($data, 'Search Suggestion');
    }

    public function getSearchResult($keyWord) {
        $data = $this->searchRepository->getSearchResult($keyWord);

        return $this->apiBaseService->sendSuccessResponse($data, 'Search Result');
    }

}
