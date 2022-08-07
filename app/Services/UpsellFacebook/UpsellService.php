<?php

namespace App\Services\UpsellFacebook;

use App\Enums\HttpStatusCode;
use App\Repositories\CustomerRepository;
use App\Repositories\MyblProductRepository;
use App\Services\Banglalink\BaseService;
use Carbon\Carbon;

class UpsellService extends BaseService {

    protected const PURCHASE_ENDPOINT = "/provisioning/provisioning/purchase";
    protected const SEND_OTP_ENDPOINT = "/send-otp";
    protected const FACEBOOK_REPORT_ENDPOINT = "/carrier_external_sales";
    
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

    public function reportFacebook($data) 
    {
        $timestamp = Carbon::now()->timestamp;
        $secret = env('FACEBOOK_SECRET_KEY', '1234');
        $carrier_id = env('FACEBOOK_CARRIER_ID', '1234');
        $access_token = env('FACEBOOK_ACCESS_TOKEN', '1234');
        $hmac = hash_hmac('sha256', $timestamp, $carrier_id, $secret);
        
        $urlWithQueryParams = self::FACEBOOK_REPORT_ENDPOINT
            . "?{$carrier_id}"
            . "&{$timestamp}"
            . "&{$hmac}"
            . "&action=buy"
            . "&{$access_token}";

        $this->setHost("https://graph.facebook.com");
        return $this->post($urlWithQueryParams, $data);
    }
}