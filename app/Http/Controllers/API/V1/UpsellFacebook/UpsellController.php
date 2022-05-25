<?php

namespace App\Http\Controllers\API\V1\UpsellFacebook;

use App\Enums\HttpStatusCode;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpsellPurchaseFinalizationRequest;
use App\Http\Requests\UpsellPurchaseInvocationRequest;
use App\Models\ProductDetail;
use App\Services\ApiBaseService;
use App\Services\Banglalink\BalanceService;
use App\Services\CustomerService;
use App\Services\ProductService;
use App\Services\UpsellFacebook\UpsellService;

class UpsellController extends Controller
{
    protected const PURCHASE_ENDPOINT = "/provisioning/provisioning/purchase";
    protected const SEND_OTP_ENDPOINT = "/send-otp";
    private $customerService, $productService, $balanceService, $upsellService;
    
    public function __construct(
        UpsellService $upsellService, 
        ProductService $productService, 
        BalanceService $balanceService,
        ApiBaseService $apiBaseService,
        CustomerService $customerService
        
    ) {
        $this->upsellService = $upsellService;
        $this->productService = $productService;
        $this->apiBaseService = $apiBaseService;
        $this->balanceService = $balanceService;
        $this->customerService = $customerService;        
    }

    public function getProductDetails($productCode) 
    {
        $productDetails = $this->upsellService->productDetails($productCode)->first();
        if(!$productDetails) {
            $msg = "Product not found";
            return $this->apiBaseService->sendErrorResponse($msg, $productDetails, HttpStatusCode::NOT_FOUND);  
        }
        $msg = "Product found successfully";
        return $this->apiBaseService->sendSuccessResponse($productDetails, $msg, [], [], HttpStatusCode::SUCCESS, 200);
    }

    /**
     * POST Request BODY
     * msisdn: string,
     * product_code: string
     * pay_with_balance: boolean|string
     */

    /*UpsellRequestProductRequest*/
    public function requestPurchase(UpsellPurchaseInvocationRequest $request)
    {
        /**
         * OTP JOURNEY
         * 
         * 1. Find Customer By Phone No
         * 2. Check if customer is eligible for the product ** ERROR
         * 3. get product cost
         * 4. check customer balance
         * 5. 4 > 3 Redirect OTP page & Send OTP to Customer Phone
         * 6. 4 < 3 Redirect Primary Error
         */

        /**
         * CURRENCY JOURNEY
         * 
         * 1. Find Customer By Phone No
         * 2. Check if customer is eligible for the product ** ERROR
         * 3. get product cost
         * 4. Redirect Payment Page
         */
        $data = [];
        $msisdn = $request->input('msisdn');
        $productCode = $request->input('product_code');

        // $customerIsEligibleForProduct = $this->upsellService->customerIsEligibleForProduct($msisdn, $productCode);
        // if(!$customerIsEligibleForProduct) {
        //     $msg = "Customer is not eligible Or Invalid product";

        //     $data['link'] = config('facebookupsell.redirect_link') 
        //         . "/upsell-error" 
        //         . "?msg={$msg}"; 
        //     return $this->apiBaseService->sendErrorResponse($msg, $data, HttpStatusCode::BAD_REQUEST);
        // }

        if(!$request->pay_with_balance) {
            $msg = "Customer buying using payment";
            $transactionId = uniqid('BLWN');
            $data['transaction_id'] = $transactionId;
            $data['app_url'] = config('facebookupsell.redirect_link') 
                . "/upsell-payment"
                . "?mobile={$msisdn}"
                . "&transaction_id={$transactionId}"
                . "&product_code={$productCode}";
            
            return $this->apiBaseService->sendSuccessResponse($data, $msg, [], [], HttpStatusCode::SUCCESS);
        }

        // $msg = "Customer buying using balance";
        // $otpToken = null;
        // $validationTime = null;
        // $res = $this->upsellService->buyWithBalance($request->input('msisdn'), $customer, $product->productCore->price, $customer->number_type, $this->balanceService);

        // if(isset($res['data']['otp_token'])) {
        //     $otpToken = $res['data']['otp_token'];
        // }

        // if(isset($res['data']['validation_time'])) {
        //     $validationTime = $res['data']['validation_time'];
        // }

        // $data['link'] = config('facebookupsell.redirect_link') 
        //     . "/upsell-otp"
        //     . "?mobile={$request->input('msisdn')}"
        //     . "&otp_token={$otpToken}"
        //     . "&validation_time={$validationTime}"
        //     . "&product_code={$productCode}";    
        
        // return $this->apiBaseService->sendSuccessResponse($data, $msg, [], HttpStatusCode::SUCCESS);
    } 
    

    /**
     * POST Request BODY
     * msisdn: string,
     * product_code: string
     */
    // public function purchaseProduct(UpsellPurchaseFinalizationRequest $request)
    // {  
    //     $msisdn      = "88" . $request->input('msisdn');
    //     $productCode = $request->input('product_code');
    
    //     $response = $this->upsellService->purchaseProduct($msisdn, $productCode);
    //     $responseData = json_decode($response['response'], true);

    //     if ($response['status_code'] != 200) {
    //         $msg = "Purchase Failed";
    //         return $this->apiBaseService->sendErrorResponse($msg, 
    //         $responseData['data']['errors'][0]['detail'], HttpStatusCode::BAD_REQUEST);
    //     }    
        
    //     $msg = "Purchase request successfully received and under process";
    //     return $this->apiBaseService->sendSuccessResponse(json_decode($response['response'], true), 
    //     $msg, [], HttpStatusCode::SUCCESS);  
    // }
}

