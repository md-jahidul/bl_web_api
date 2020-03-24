<?php

/**
 * User: Bulbul Mahmud Nito
 * Date: 23/03/2020
 */

namespace App\Services\Banglalink;

use App\Services\ApiBaseService;
use App\Repositories\RoamingCategoryRepository;
use App\Repositories\RoamingOperatorRepository;
use App\Repositories\RoamingGeneralPageRepository;
use Illuminate\Http\Response;

class RoamingService {

    /**
     * @var $catRepo
     * @var $gnPageRepo
     * @var $operatorRepo
     */
    protected $catRepo;
    protected $gnPageRepo;
    protected $operatorRepo;
    public $responseFormatter;

    /**
     * RoamingService constructor.
     * @param RoamingCategoryRepository $catRepo
     * @param RoamingGeneralPageRepository $gnPageRepo
     * @param RoamingOperatorRepository $operatorRepo
     */
    public function __construct(
    ApiBaseService $responseFormatter, RoamingCategoryRepository $catRepo, RoamingGeneralPageRepository $gnPageRepo, RoamingOperatorRepository $operatorRepo
    ) {
        $this->catRepo = $catRepo;
        $this->gnPageRepo = $gnPageRepo;
        $this->operatorRepo = $operatorRepo;
        $this->responseFormatter = $responseFormatter;
    }

    /**
     * Get roaming categories
     * @return Response
     */
    public function getCategories() {
        $response = $this->catRepo->getCategoryList();
        return $this->responseFormatter->sendSuccessResponse($response, 'Roaming Category List');
    }

    /**
     * Get roaming categories
     * @return Response
     */
    public function roamingGeneralPage($pageSlug) {
        $response = $this->gnPageRepo->roamingGeneralPage($pageSlug);
        return $this->responseFormatter->sendSuccessResponse($response, 'Roaming Category List');
    }

    /**
     * Get roaming country
     * @return Response
     */
    public function getCountries() {
        $response = $this->operatorRepo->getCountries();
        return $this->responseFormatter->sendSuccessResponse($response, 'Roaming Country List');
    }

    /**
     * Get roaming operators by country name
     * @return Response
     */
    public function getOperators($countryEn) {
        $response = $this->operatorRepo->getOperators($countryEn);
        return $this->responseFormatter->sendSuccessResponse($response, 'Roaming Operator List');
    }

}
