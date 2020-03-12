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
        $keywords = $this->popularRepository->getResults();
        return $this->apiBaseService->sendSuccessResponse($keywords, 'Popular Search Result');
    }

    public function searchSuggestion($keyword) {

        $keywords = $this->searchRepository->searchSuggestion($keyword);


        $data = [];

        $heads = array(
            'prepaid-internet' => "Prepid Internet",
            'prepaid-voice' => "Prepid Voice",
            'prepaid-bundle' => "Prepid Bundle",
            'postpaid-internet' => "Postpaid Internet",
            'others' => "Others"
        );
        $count = 0;
        foreach ($keywords as $val) {
            foreach ($val as $k) {
                $data[$k->type]['head'] = $heads[$k->type];
                $data[$k->type]['keywords'][$count]['keyword'] = $k->keyword;
                $data[$k->type]['keywords'][$count]['product_url'] = $k->product_url;
                $count++;
            }
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
        $count = 0;
        foreach ($keywords as $k) {
            $data[$k->type]['head'] = $heads[$k->type];
            $data[$k->type]['keywords'][$count]['keyword'] = $k->keyword;
            $data[$k->type]['keywords'][$count]['product_url'] = $k->product_url;
        }
        return $this->apiBaseService->sendSuccessResponse($data, 'Search Suggestion');
    }

    public function getSearchResult($keyWord) {
        $data = $this->searchRepository->getSearchResult($keyWord);

        return $this->apiBaseService->sendSuccessResponse($data, 'Search Result');
    }

}
