<?php

namespace App\Services\UpsellFacebook;

use App\Enums\HttpStatusCode;
use App\Repositories\CustomerRepository;
use App\Repositories\MyblProductRepository;
use App\Services\Banglalink\BaseService;

class UpsellService extends BaseService {

    protected const PURCHASE_ENDPOINT = "/provisioning/provisioning/purchase";
    protected const SEND_OTP_ENDPOINT = "/send-otp";
    
    public function buyWithBalance(
        $msisdn,
        $customer,
        $productPrice, 
        $customerType, 
        $balanceService
    ) {
        if($customerType == 'prepaid'){
            $customerBalance = $balanceService->getPrepaidBalance($customer->id);

            if ($productPrice > $customerBalance) {
                return false;
            }
        }
        
        if($customerType == 'postpaid'){
            $customerBalance = $balanceService->getPostpaidBalance($customer->id);

            if ($productPrice > $customerBalance) {
                return false;
            }
        }

        $res = $this->sendOtp($msisdn);

        return $res;
    }

    public function sendOtp($msisdn) 
    {
        $this->post(self::SEND_OTP_ENDPOINT, ['phone' => $msisdn]);
    }

    public function productDetails($productCode) 
    {
        $myblProductRepository = resolve(MyblProductRepository::class);
        return $myblProductRepository->getProduct($productCode);
    }

    public function customerDetails($msisdn) 
    {
        $customerRepository = resolve(CustomerRepository::class);
        return $customerRepository->getCustomerInfoByPhone($msisdn);
    }

    public function customerIsEligibleForProduct($msisdn, $productCode) 
    {
        $customerDetails = $this->customerDetails($msisdn);

        /**
         * TODO:
         * Look into Purchase Service of Mybl Api, 
         * Method purchaseProduct 
         * Line 134, 165
         */
    }

    public function purchaseProduct($msisdn, $productCode) 
    {
        $param = [
            "channel" => env('PURCHASE_CHANNEL_NAME', 'website'),
            'id' => $productCode,
            'msisdn' => $msisdn
        ];
    
        return $this->post(self::PURCHASE_ENDPOINT, $param);        
    }
}