<?php
/**
 * Created by PhpStorm.
 * User: jahangir
 * Date: 12/22/19
 * Time: 10:47 AM
 */

namespace App\Http\Controllers\API\V1;


use App\Http\Controllers\Controller;
use App\Services\SearchService;

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

    public function getSearchResult($keyWord)
    {
        return $this->searchService->getSearchResult($keyWord);
    }


}
