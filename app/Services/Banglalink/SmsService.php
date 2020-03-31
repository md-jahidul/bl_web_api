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

    /**
     * Request for getting fnf list
     *
     * @param Request $request
     * @return string
     */
    public function getFnfList(Request $request)
    {
        $customer = $this->customerService->getAuthenticateCustomer($request);

        if (!$customer) {
            return $this->apiBaseService->sendErrorResponse("Token Invalid", [], HttpStatusCode::UNAUTHORIZED);
        }

        $customer_info = $this->blCustomerService->getCustomerInfoByNumber($customer->phone);

        $customer_id = $customer_info->getData()->data->package->customerId;

        $package = $customer_info->getData()->data->package->code;

        $end_point = self::CUSTOMER_ENDPOINT . "/" . $customer_id . "/friend-and-family?packageName=" . $package;

        $result = $this->get($end_point);


        if ($result['status_code'] == 200) {
            $data = $this->getFormattedData($result['response']);

            return $this->apiBaseService->sendSuccessResponse(
                $data,
                "Fnf list",
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

    public function sendSms(Request $request)
    {
        $msisdn = $request->input('msisdn');
        $message = $request->input('message');

        $end_point = self::SMS_ENDPOINT . "?message=" . $message."&msisdn=".$msisdn;

        $result = $this->get($end_point);

        if ($result['status_code'] == 200) {
            $data = $this->getFormattedData($result['response']);

            return $this->apiBaseService->sendSuccessResponse(
                $data,
                "SMS Send Successfully",
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
