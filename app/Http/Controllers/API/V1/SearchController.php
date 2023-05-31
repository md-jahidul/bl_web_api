<?php
/**
 * Dev: Bulbul Mahmud Nito
 * Date: 10/03/2020
 */

namespace App\Http\Controllers\API\V1;


use App\Http\Controllers\Controller;
use App\Services\SearchService;
use \Illuminate\Http\Request;
class SearchController extends Controller
{
    /**
     * @var SearchService
     */
    protected $searchService;

    /**
     * SearchController constructor.
     * @param SearchService $searchService
     */
    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    public function getPopularSearch(){
        return $this->searchService->popularSearch();
    }

    public function getSearchSuggestion($keyword){
        return $this->searchService->searchSuggestion($keyword);
    }

    public function getSearchData(Request $request){
        return $this->searchService->searchData($request);
    }




}
