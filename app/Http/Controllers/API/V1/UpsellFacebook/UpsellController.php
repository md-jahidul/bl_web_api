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
use Carbon\Carbon;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\Boolean;

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
     * {
     *  "tid": "ATjxtPq3GZiJQaq_aopM7u544_MhK_V-WtHwYF6yPnwuCEScbRzem-vDspAqmxxr4bA",
     *  "result_code": "0",
     *  "result_message": "success",
     *      "product": {
     *      "product_name": "Unlimited Facebook For 1 day",
     *      "product_code": "62",
     *      "product_is_loan:" true,
     *      "product_price": "6",
     *      "product_currency": "USD",
     *      "product_description": "Unlimited Facebook For 1 day for 6 USD"
     *  }
     * }
     */
    public function reportFacebook(Request $request) 
    {
        $data = $this->upsellService->reportPurchase($request->all());
        $response =  json_decode($data['response'], true);

        if(isset($response['error'])) {
            return $this->apiBaseService->sendErrorResponse('Failed', $response['error'], HttpStatusCode::BAD_REQUEST);
        }  

        return $this->apiBaseService->sendSuccessResponse($response, 'Success', [], [], HttpStatusCode::SUCCESS);
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
        $fbTransactionId = $request->input('fb_transaction_id');
        $productDetails = $this->upsellService->productDetails($productCode)->first()->toArray();
        $productMrpPrice = $productDetails['details']['mrp_price'];
        $productValidity = $productDetails['details']['validity'];
        $productDisplayTitleEn = $productDetails['details']['display_title_en']; 
        // str_replace(' ', '%20', $productDetails['details']['display_title_en']);
        
        $secret = env("UPSELL_SECRET");
        $timestamp = Carbon::now()->timestamp;
        $signature = strrev(base64_encode($timestamp * $timestamp));
        $hash = hash_hmac('sha256', $signature, $secret, true);
        $signature = $signature . $timestamp . $hash;
        

        // $customerIsEligibleForProduct = $this->upsellService->customerIsEligibleForProduct($msisdn, $productCode);
        // if(!$customerIsEligibleForProduct) {
        //     $msg = "Customer is not eligible Or Invalid product";

        //     $data['link'] = config('facebookupsell.redirect_link') 
        //         . "/upsell-error" 
        //         . "?msg={$msg}"; 
        //     return $this->apiBaseService->sendErrorResponse($msg, $data, HttpStatusCode::BAD_REQUEST);
        // }

        // $decode = base64_decode(strrev(str_replace('1660463468', '==', '1660463468ANyAzN4UjM2UDOykDOzEzN1cjM')))/1660463468;

        if($request->pay_with_balance == false) {
            $msg = "Customer buying using payment";
            $sslChannel = env("SSL_TRX_ID_FOR_UPSELL", 'BLWN');
            $fbTrxId = $fbTransactionId;
            $sslTrxId = uniqid($sslChannel);
            $data['fb_trx_id'] = $fbTrxId;
            $data['ssl_trx_id'] = $sslTrxId;
            $data['app_url'] = config('facebookupsell.redirect_link') 
                . "/upsell-payment"
                . "?mobile={$msisdn}"
                . "&ssl_trx_id={$sslTrxId}"
                . "&fb_trx_id={$fbTrxId}"
                . "&product_code={$productCode}"
                . "&product_price={$productMrpPrice}"
                . "&product_validity={$productValidity}"
                . "&product_display_title_en={$productDisplayTitleEn}"
                . "&signature={$signature}"
                . "&timestamp={$timestamp}";
            
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

