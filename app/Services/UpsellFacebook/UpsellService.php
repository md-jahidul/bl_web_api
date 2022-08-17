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
    protected const CUSTOMER_INFO_API_ENDPOINT = "/customer-information/customer-information";
    
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
        $channelName = 'website';
        $customerId = $this->customerDetails($msisdn)->id;
        $url = self::CUSTOMER_INFO_API_ENDPOINT . '/' . $customerId . '/available-products?channel=' . $channelName;
        $response = $this->get($url);

        /**
         * TODO:
         * Look into Purchase Service of Mybl Api, 
         * Method purchaseProduct 
         * Line 134, 165
         */
        dd(json_decode($response['response']));

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

    public function reportPurchase($data) 
    {
        $timestamp = Carbon::now()->timestamp;
        $secret = env('FACEBOOK_SECRET_KEY', '1234');
        $carrier_id = env('FACEBOOK_CARRIER_ID', '1234');
        $access_token = env('FACEBOOK_ACCESS_TOKEN', '1234');
        $hmac = hash_hmac('sha256', $timestamp . $carrier_id, $secret);
        
        $urlWithQueryParams = self::FACEBOOK_REPORT_ENDPOINT
            . "?carrier_id={$carrier_id}"
            . "&timestamp={$timestamp}"
            . "&hmac={$hmac}"
            . "&action=buy"
            . "&access_token={$access_token}";

        $this->setHost("https://graph.facebook.com");
        // dd($this->getHost().$urlWithQueryParams);
        return $this->post($urlWithQueryParams, $data);
    }
}