<?php

/**
 * Dev: Bulbul Mahmud Nito
 * Date: 10/03/2020
 */

namespace App\Services;

use App\Repositories\AdTechRepository;
use App\Repositories\PopularSearchRepository;
use App\Repositories\ProductRepository;
use App\Repositories\SearchRepository;
use App\Services\Banglalink\BaseService;

class SearchService extends BaseService {

    /**
     * @var $popularRepository
     * @var $searchRepository
     */
    protected $popularRepository;
    protected $searchRepository;
    protected $productRepository;
    protected $adTechRepository;

    /**
     * @var ApiBaseService
     */
    protected $apiBaseService;

    /**
     * SearchService constructor.
     * @param PopularSearchRepository $popularRepository
     * @param SearchRepository $searchRepository
     */
    public function __construct(SearchRepository $searchRepository, PopularSearchRepository $popularRepository, ApiBaseService $apiBaseService, ProductRepository $productRepository, AdTechRepository $adTechRepository) {
        $this->popularRepository = $popularRepository;
        $this->searchRepository = $searchRepository;
        $this->apiBaseService = $apiBaseService;
        $this->productRepository = $productRepository;
        $this->adTechRepository = $adTechRepository;
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

        $keyword = str_replace(['-', '_', '\'', '@'], ' ', $keyword->keyword);

        $keywords = $this->searchRepository->searchSuggestion($keyword);

        // AdTech
        $adTech = $this->adTechRepository->getSearchAdTech('search_modal');

        #code array for get the product list
        $product_code_array = [];
        $response = [];
        $result = $keywords->reject(function ($product){
            return $product->product_code;
        });

        $response['search_result'] = array_values($result->toArray());

        $count = 0;
        foreach ($keywords as $k => $val) {
            if ($val->product_code != "") {
                $data[$val->product_code] = $val;
            }

           if ($val->product_code) {
            $product_code_array[] = $val->product_code;
           }
        }

        $products = $this->productRepository->getProductInfoByCode($product_code_array);

        foreach ($products as $key => $product) {
            $row = [];
            $row['id'] = $product->id;
            $row['product_code'] = $product->product_code;
            $row['url_slug'] = $product->url_slug;
            $row['name_en'] = $product->name_en;
            $row['name_bn'] = $product->name_bn;
            $row['bonus'] = $product->bonus;
            $row['point'] = $product->point;
            $row['price'] = $product->productCore->price;
            $row['price_tk'] = $product->productCore->price_tk;
            $row['validity_days'] = $product->productCore->validity_days;
            $row['validity_unit'] = $product->productCore->validity_unit;
            $row['internet_volume_mb'] = $product->productCore->internet_volume_mb;
            $row['sms_volume'] = $product->productCore->sms_volume;
            $row['minute_volume'] = $product->productCore->minute_volume;
            $row['callrate_offer'] = $product->productCore->callrate_offer;
            $row['call_rate_unit'] = $product->productCore->call_rate_unit;
            $row['sms_rate_offer'] = $product->productCore->sms_rate_offer;
            $row['sim_type'] = $product->sim_category->alias;
            $row['offer_type'] = $product->offer_category->alias;
            $row['keyword'] = $data[$product->product_code]['keyword'];
            $row['product_url'] = $data[$product->product_code]['product_url'];
            $row['type'] = $data[$product->product_code]['type'];
            $response['keyword_sections']['products'][] = $row;
        }

        #For Add tag
        $response['keyword_sections']['adTech'] = $adTech;
        return $this->apiBaseService->sendSuccessResponse($response, 'Search Data');
    }

    public function getSearchResult($keyWord) {
        $data = $this->searchRepository->getSearchResult($keyWord);

        return $this->apiBaseService->sendSuccessResponse($data, 'Search Result');
    }

}
