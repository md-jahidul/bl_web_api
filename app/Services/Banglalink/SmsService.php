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
class SmsService extends BaseService
{

    protected $apiBaseService;
    protected $customerService;
    protected $blCustomerService;

    protected const SMS_ENDPOINT   = "/octopus-sms/sms";


    /**
     * FnfService constructor.
     * @param ApiBaseService $apiBaseService
     * @param CustomerService $customerService
     * @param BanglalinkCustomerService $blCustomerService
     */
    public function __construct(ApiBaseService $apiBaseService, CustomerService $customerService, BanglalinkCustomerService $blCustomerService)
    {
        $this->apiBaseService = $apiBaseService;
        $this->customerService = $customerService;
        $this->blCustomerService = $blCustomerService;
    }


    public function sendSms(Request $request)
    {
        $msisdn = $request->input('msisdn');
        $message = $request->input('message');

        $end_point = self::SMS_ENDPOINT . "?message=" . $message."&msisdn=".$msisdn;

        $result = $this->get($end_point);

        if ($result['status_code'] == 202) {

            return $this->apiBaseService->sendSuccessResponse(
                [],
                "SMS Send Successfully",
                [],
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
