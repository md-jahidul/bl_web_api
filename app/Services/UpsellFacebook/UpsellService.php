<?php

namespace App\Services\UpsellFacebook;

use App\Enums\HttpStatusCode;
use App\Repositories\CustomerRepository;
use App\Repositories\MyblProductRepository;
use App\Services\ApiCallService;
use App\Services\Banglalink\BaseService;
use Carbon\Carbon;

use function PHPSTORM_META\type;

class UpsellService extends BaseService {

    protected const SEND_OTP_ENDPOINT = "/send-otp";
    protected const FACEBOOK_REPORT_ENDPOINT = "/carrier_external_sales";
    protected const PURCHASE_ENDPOINT = "/provisioning/provisioning/purchase";
    protected const CUSTOMER_INFO_API_ENDPOINT = "/customer-information/customer-information";

    private $apiCallService;

    public function __construct()
    {
        $this->apiCallService = resolve(ApiCallService::class);
    }
    
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
        /**
         * TODO:
         * Look into Purchase Service of Mybl Api, 
         * Method purchaseProduct 
         * Line 134, 165
         */

        $channelName = 'website';
        $customerId = $this->customerDetails($msisdn)->id;
        
        $url = self::CUSTOMER_INFO_API_ENDPOINT . '/' . $customerId . '/available-products?channel=' . $channelName;
        $response = $this->get($url);
        $availableProducts = collect(json_decode($response['response']))->pluck('code');
        return $availableProducts->containsStrict($productCode);
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
        $timestamp = (string) Carbon::now()->timestamp;
        $secret = config('facebookupsell.fb_upsell_secret');
        $carrier_id = config('facebookupsell.fb_carrier_id');
        $access_token = config('facebookupsell.fb_access_token');
        $hmac = hash_hmac('sha256', $timestamp . $carrier_id, $secret);
        
        $urlWithQueryParams = self::FACEBOOK_REPORT_ENDPOINT
            . "?carrier_id={$carrier_id}"
            . "&timestamp={$timestamp}"
            . "&hmac={$hmac}"
            . "&action=buy"
            . "&access_token={$access_token}";

        // dump($data);
        // dump($urlWithQueryParams);
        // die;

        // https://graph.facebook.com/carrier_external_sales
        // ?carrier_id=1160
        // &timestamp=1662545690
        // &hmac=b96e4b00f064146acac4b551a727f941f46b8050a2958227e0a016b233f1394f
        // &action=buy
        // &access_token=334240057380646|oAjaUFtk-8rBesGj1mzcpEqXhfA

        $this->apiCallService->setHost("https://graph.facebook.com");
        return $this->apiCallService->post($urlWithQueryParams, $data);
    }
}