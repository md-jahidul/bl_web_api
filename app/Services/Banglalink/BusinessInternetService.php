<?php

/**
 * User: Bulbul Mahmud Nito
 * Date: 13/02/2020
 */

namespace App\Services\Banglalink;

use App\Services\ApiBaseService;
use App\Repositories\BusinessInternetRepository;
use Illuminate\Http\Response;

class BusinessInternetService {


    /**
     * @var $internetRepo
     */
    protected $internetRepo;
    public $responseFormatter;

    /**
     * BusinessInternetService constructor.
     * @param BusinessInternetRepository $internetRepo
     */
    public function __construct(ApiBaseService $responseFormatter, BusinessInternetRepository $internetRepo) {
        $this->internetRepo = $internetRepo;
        $this->responseFormatter = $responseFormatter;
    }

    /**
     * Get Internet package
     * @return Response
     */
    public function getInternetPackage() {
        $response = $this->internetRepo->getInternetPackageList();
        return $this->responseFormatter->sendSuccessResponse($response, 'Business Internet Package List');
    }



}