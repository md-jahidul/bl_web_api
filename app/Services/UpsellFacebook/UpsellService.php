<?php

namespace App\Services\UpsellFacebook;

use App\Enums\HttpStatusCode;
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

        $res = $this->post(self::SEND_OTP_ENDPOINT, ['phone' => $msisdn]);

        return $res;
    }

    public function buyWithMoney() 
    {
        
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

    public function sendOtp($msisdn) 
    {
        
    }
}