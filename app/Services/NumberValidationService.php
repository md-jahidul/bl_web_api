<?php
/**
 * Created by PhpStorm.
 * User: jahangir
 * Date: 11/24/19
 * Time: 11:19 AM
 */

namespace App\Services;


use App\Enums\HttpStatusCode;
use App\Services\Banglalink\BanglalinkCustomerService;

class NumberValidationService extends ApiBaseService
{
    /**
     * @var BanglalinkCustomerService
     */
    protected $blCustomerService;

    /**
     * NumberValidationService constructor.
     * @param BanglalinkCustomerService $blCustomerService
     */
    public function __construct(BanglalinkCustomerService $blCustomerService)
    {
        $this->blCustomerService = $blCustomerService;
    }


    /**
     * Validate number
     *
     * @param $number
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateNumber($number)
    {
        $missdn = "88" . $number;

        $customer = $this->blCustomerService->getCustomerInfoByNumber($missdn);


        if ($customer->getData()->status == "FAIL") {
            return $this->sendErrorResponse(
                "Something went wrong",
                [],
                HttpStatusCode::INTERNAL_ERROR
            );
        }

        if ($customer->getData()->data->status == "active") {
            return $this->sendSuccessResponse(
                [],
                "Number is Valid",
                [],
                HttpStatusCode::SUCCESS
            );
        } else {
            return $this->sendErrorResponse(
                "Number is Not Valid",
                [],
                HttpStatusCode::VALIDATION_ERROR
            );
        }
    }

    /**
     * Validate number
     *
     * @param $number
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateNumberWithResponse($number)
    {
        $missdn = "88" . $number;

        $customer = $this->blCustomerService->getCustomerInfoByNumber($missdn);


        if ($customer->getData()->status == "FAIL") {
            return $this->sendErrorResponse(
                "Not a Valid Banglalink Number",
                [],
                HttpStatusCode::INTERNAL_ERROR
            );
        }

        if ($customer->getData()->data->status == "active") {
            return $this->sendSuccessResponse(
//                [],
                $customer->getData()->data,
                "Number is Valid",
                [],
                HttpStatusCode::SUCCESS
            );
        } else {
            return $this->sendErrorResponse(
                "Number is Not Valid. Status: ". $customer->getData()->data->status == "active",
                [],
                HttpStatusCode::VALIDATION_ERROR
            );
        }
    }
}
