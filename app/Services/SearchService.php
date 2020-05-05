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

        $heads = array(
            'prepaid-internet' => "Prepid Internet",
            'prepaid-voice' => "Prepid Voice",
            'prepaid-bundle' => "Prepid Bundle",
            'postpaid-internet' => "Postpaid Internet",
            'others' => "Others"
        );



        $data = [];
        $response = [];
        $response['more_result'] = 0;

        $count = 0;
        foreach ($keywords as $k => $val) {
            if ($val->type != "") {
                $data[$val->type][$count] = $val;
                $count++;
            }
        }

        $catCount = -1;
        $catName = "";
        foreach ($data as $k => $kw) {

            if ($catName != $k) {
                $catName = $k;
                $catCount++;
            }
            $response['keyword_sections'][$catCount]['category'] = $heads[$k];
            $count = 0;
            foreach ($kw as $val) {
                if ($count < $limits[$k]) {
                    $response['keyword_sections'][$catCount]['keywords'][$count] = $val;
                    $count++;
                } else {
                    $response['more_result'] = 1;
                }
            }
            $catName = $k;
        }

        return $this->apiBaseService->sendSuccessResponse($response, 'Search Suggestion');
    }

    public function searchData($keyword) {

        $keywords = $this->searchRepository->searchSuggestion($keyword);

        $heads = array(
            'prepaid-internet' => "Prepid Internet",
            'prepaid-voice' => "Prepid Voice",
            'prepaid-bundle' => "Prepid Bundle",
            'postpaid-internet' => "Postpaid Internet",
            'others' => "Others"
        );



        $data = [];
        $response = [];
        $count = 0;
        foreach ($keywords as $k => $val) {
            if ($val->type != "") {
                $data[$val->type][$count] = $val;
                $count++;
            }
        }

        $catCount = -1;
        $catName = "";
        foreach ($data as $k => $kw) {

            if ($catName != $k) {
                $catName = $k;
                $catCount++;
            }
            $response['keyword_sections'][$catCount]['category'] = $heads[$k];
            $count = 0;
            foreach ($kw as $val) {
                $response['keyword_sections'][$catCount]['keywords'][$count] = $val;
                $count++;
            }
            $catName = $k;
        }

        return $this->apiBaseService->sendSuccessResponse($response, 'Search Data');
    }

    public function getSearchResult($keyWord) {
        $data = $this->searchRepository->getSearchResult($keyWord);

        return $this->apiBaseService->sendSuccessResponse($data, 'Search Result');
    }

}
