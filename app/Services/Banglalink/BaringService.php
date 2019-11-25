<?php

namespace App\Services\Banglalink;

use App\Contracts\ApiBaseServiceInterface;
use App\Enums\HttpStatusCode;
use App\Services\ApiBaseService;
use App\Services\CustomerService;
use Illuminate\Http\Request;

/**
 * Class FnfService
 * @package App\Services\Banglalink
 */
class BaringService extends BaseService
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
    public function __construct(ApiBaseServiceInterface $apiBaseService, CustomerService $customerService, BanglalinkCustomerService $blCustomerService)
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
    public function getBaringService(Request $request)
    {
        $customer = $this->customerService->getAuthenticateCustomer($request);

        if (!$customer) {
            return $this->apiBaseService->sendErrorResponse("Token Invalid", [], HttpStatusCode::UNAUTHORIZED);
        }

        $customer_info = $this->blCustomerService->getCustomerInfoByNumber($customer->phone);

        $customer_id = $customer_info->getData()->data->package->customerId;

        $end_point = self::CUSTOMER_ENDPOINT . "/" . $customer_id . "/barrings";

        $result = $this->get($end_point);


        if ($result['status_code'] == 200) {
            $data = $this->getFormattedData($result['response']);

            return $this->apiBaseService->sendSuccessResponse(
                $data,
                "Baring Service",
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
        $barService = json_decode($data, true);

        return $barService[0];
    }


}
