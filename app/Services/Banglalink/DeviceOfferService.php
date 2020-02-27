<?php

namespace App\Services\Banglalink;

use App\Repositories\DeviceOfferRepository;
use App\Services\ApiBaseService;


class DeviceOfferService extends BaseService
{
    /**
     * @var ApiBaseService
     */
    public $deviceOfferRepository;
    public $responseFormatter;

  

    public function __construct
    (
        ApiBaseService $apiBaseService,
        DeviceOfferRepository $deviceOfferRepository

    ) {
        $this->deviceOfferRepository = $deviceOfferRepository;
        $this->responseFormatter = $apiBaseService;
    }

    public function getOfferList($brand){
        $response = $this->deviceOfferRepository->getList($brand);
        return $this->responseFormatter->sendSuccessResponse($response, 'Device Offer List');
    }
   

}
