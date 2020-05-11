<?php

namespace App\Services\Banglalink;

use App\Enums\HttpStatusCode;

use App\Exceptions\AmarOfferBuyException;
use App\Exceptions\IdpAuthException;

use App\Repositories\AmarOfferDetailsRepository;
use App\Services\ApiBaseService;
use App\Services\CustomerService;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BondhoSimOfferService extends BaseService
{
    /**
     * @var ApiBaseService
     */
    public $amarOfferDetailsRepository;
    public $productService;
    public $responseFormatter;
    protected const BONDHO_SIM_OFFER_API_ENDPOINT = "/product-offer/offer/bandho-sim";
    /**
     * @var CustomerService
     */
    protected $customerService;

    public function __construct
    (
        ApiBaseService $apiBaseService,
        CustomerService $customerService,
        AmarOfferDetailsRepository $amarOfferDetailsRepository

    ) {
        $this->amarOfferDetailsRepository = $amarOfferDetailsRepository;
        $this->responseFormatter = $apiBaseService;
        $this->customerService = $customerService;
    }

    public function getBondhoSimOfferUrl($msisdn)
    {
        return self::BONDHO_SIM_OFFER_API_ENDPOINT . "?" . "msisdn=$msisdn";
    }

    /**
     * @param $mobile
     * @return JsonResponse
     */
    public function getBondhoSimOfferResponse($mobile)
    {
        $response_data = $this->get($this->getBondhoSimOfferUrl($mobile));

        if ($response_data['status_code'] == 200){
            $response = json_decode($response_data['response'], true);
            return $this->responseFormatter->sendSuccessResponse($response, 'Bondho SIM Offer Response');
        }

        return $this->responseFormatter->sendErrorResponse('Internal Server error', 500);
    }

}
