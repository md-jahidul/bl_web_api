<?php

namespace App\Services\Banglalink;

use App\Enums\HttpStatusCode;
use App\Services\ApiBaseService;
use App\Services\CustomerService;
use Illuminate\Http\Request;

/**
 * Class FnfService
 * @package App\Services\Banglalink
 */
class BanglalinkCustomerService extends BaseService
{

    protected $apiBaseService;
    protected $customerService;
    protected const CUSTOMER_ENDPOINT   = "/customer-information/customer-information";


    /**
     * FnfService constructor.
     * @param ApiBaseService $apiBaseService
     * @param CustomerService $customerService
     */
    public function __construct(ApiBaseService $apiBaseService, CustomerService $customerService)
    {
        $this->apiBaseService = $apiBaseService;
        $this->customerService = $customerService;
    }


    /**
     * Get Customer Info
     *
     * @param $number
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCustomerInfoByNumber($number)
    {
        $end_point = self::CUSTOMER_ENDPOINT . "/" . "?include=SUBSCRIPTION_TYPES&msisdn=" . $number;

        $result = $this->get($end_point);

        if ($result['status_code'] == 200) {
            return $this->apiBaseService->sendSuccessResponse(
                json_decode($result['response'], true),
                "Customer Info",
                [],
                HttpStatusCode::SUCCESS
            );
        }

        return $this->apiBaseService->sendErrorResponse(
            "Internal server error",
            [],
            HttpStatusCode::INTERNAL_ERROR
        );
    }

}
