<?php

namespace App\Services\Banglalink;

use App\Repositories\EasyPaymentCardRepository;
use App\Services\ApiBaseService;

class EasyPaymentCardService extends BaseService
{
    /**
     * @var ApiBaseService
     */
    public $paymentCardRepository;
    public $responseFormatter;

  

    public function __construct
    (
        ApiBaseService $apiBaseService,
        EasyPaymentCardRepository $paymentCardRepository

    ) {
        $this->paymentCardRepository = $paymentCardRepository;
        $this->responseFormatter = $apiBaseService;
    }

    public function getCardList($division, $area){
        $response = $this->paymentCardRepository->getList($division, $area);
        return $this->responseFormatter->sendSuccessResponse($response, 'Easy Payment Card List');
    }
    
    public function getAreaList($division){
        $response = $this->paymentCardRepository->getAreas($division);
        return $this->responseFormatter->sendSuccessResponse($response, 'Area List by Division');
    }

}
