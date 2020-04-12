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
class FnfService extends BaseService
{

    protected $apiBaseService;
    protected $customerService;
    protected $blCustomerService;

    protected const CUSTOMER_ENDPOINT   = "/customer-information/customer-information";


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


    /**
     * Formatted Fnf data
     *
     * @param $data
     * @return mixed
     */
    public function getFormattedData($data)
    {
        $fnfList = json_decode($data, true);

        $super_fnf = [];

        foreach (array_filter($fnfList[0]) as $key => $value) {
            if (preg_match('/fafMsisdn[0-9]/', $key)) {
                array_push($super_fnf, ['msisdn' => $value]);
            }
        }

        $fnf = [];

        foreach (array_filter($fnfList[1]) as $key => $value) {
            if (preg_match('/fafMsisdn[0-9]/', $key)) {
                array_push($fnf, ['msisdn' => $value]);
            }
        }

        $final_data['super_fnf'] = $super_fnf;

        $final_data['fnf'] =  $fnf;

        $final_data['remaining_fnf'] = count($fnfList[1]) - count(array_filter($fnfList[1]));

        return $final_data;
    }


}
