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
     * Give Internet like and get total count
     * @return Response
     */
    public function saveInternetLike($internetId) {
        $response = $this->internetRepo->internetLike($internetId);
        return $this->responseFormatter->sendSuccessResponse($response, 'Business Internet Package Likes');
    }



}
