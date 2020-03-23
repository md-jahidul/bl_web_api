<?php

/**
 * User: Bulbul Mahmud Nito
 * Date: 23/03/2020
 */

namespace App\Services\Banglalink;

use App\Services\ApiBaseService;
use App\Repositories\RoamingCategoryRepository;
use Illuminate\Http\Response;

class RoamingService {

    /**
     * @var $catRepo
     */
    protected $catRepo;
    public $responseFormatter;

    /**
     * RoamingService constructor.
     * @param RoamingCategoryRepository $catRepo
     */
    public function __construct(
    ApiBaseService $responseFormatter, RoamingCategoryRepository $catRepo
    ) {
        $this->catRepo = $catRepo;
        $this->responseFormatter = $responseFormatter;
    }

    /**
     * Get business categories
     * @return Response
     */
    public function getCategories() {
        $response = $this->catRepo->getCategoryList();
        return $this->responseFormatter->sendSuccessResponse($response, 'Roaming Category List');
    }

}
