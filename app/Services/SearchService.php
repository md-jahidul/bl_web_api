<?php
/**
 * Created by PhpStorm.
 * User: jahangir
 * Date: 12/22/19
 * Time: 11:10 AM
 */

namespace App\Services;


use App\Repositories\SearchRepository;
use App\Services\Banglalink\BaseService;

class SearchService extends BaseService
{
    /**
     * @var SearchRepository
     */
    protected $searchRepository;

    /**
     * @var ApiBaseService
     */
    protected $apiBaseService;

    /**
     * SearchService constructor.
     * @param SearchRepository $searchRepository
     */
    public function __construct(SearchRepository $searchRepository, ApiBaseService $apiBaseService)
    {
        $this->searchRepository = $searchRepository;
        $this->apiBaseService = $apiBaseService;
    }


    public function getSearchResult($keyWord)
    {
        $data = $this->searchRepository->getSearchResult($keyWord);

        return $this->apiBaseService->sendSuccessResponse($data, 'Search Result');
    }
}
