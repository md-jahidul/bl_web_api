<?php

namespace App\Http\Controllers\API\V1\UpsellFacebook;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Enums\HttpStatusCode;
use App\Services\ApiBaseService;
use App\Services\ProductService;
use App\Services\CustomerService;
use App\Http\Controllers\Controller;
use App\Services\Banglalink\BalanceService;
use App\Services\UpsellFacebook\UpsellService;
use App\Http\Requests\UpsellPurchaseInvocationRequest;

class UpsellController extends Controller
{
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
     * {
     *  "msisdn": string,
     *  "product_code": string
     *  "pay_with_balance": boolean|string
     * }
     */
    public function requestPurchase(UpsellPurchaseInvocationRequest $request)
    {
        $data = [];
        $msisdn = $request->input('msisdn');
        $productCode = $request->input('product_code');
        $fbTransactionId = $request->input('fb_transaction_id');
        $productDetails = (object) $request->input('product_details');
        
        $productPriceWithUnitStr = "{$productDetails->price} {$productDetails->currency}";
        $productValidityWithUnitStr = "{$productDetails->time_amount} {$productDetails->time_unit}";

        $productPrice = rawurlencode($productPriceWithUnitStr);
        $productValidity = rawurlencode($productValidityWithUnitStr);
        $productDisplayTitle = rawurlencode("{$productDetails->name}");

        $sslChannel = env("SSL_TRX_ID_FOR_UPSELL", 'BLWN');
        $sslTrxId = uniqid($sslChannel);

        // if(! $this->upsellService->customerIsEligibleForProduct($msisdn, $productCode)) {
        //     $msg = "Customer is not Eligible";
        //     return $this->apiBaseService->sendErrorResponse($msg, [], HttpStatusCode::VALIDATION_ERROR);
        // }
        
        // if (! is_null($product)) {
        //     $productDetails = $product->toArray();
        // } else {
        //     $msg = "Product Not Found";
        //     return $this->apiBaseService->sendErrorResponse($msg, [], HttpStatusCode::VALIDATION_ERROR);
        // }

        $secret = config('facebookupsell.bl_upsell_secret');
        $timestamp = Carbon::now()->timestamp;
        $strToHash = $timestamp 
                   . $productCode 
                   . $productPriceWithUnitStr 
                   . $productValidityWithUnitStr 
                   . $sslTrxId 
                   . $fbTransactionId
                   . $msisdn;
        $base64StrToHash = base64_encode($strToHash);
        $hash = hash_hmac('sha256', $base64StrToHash, $secret);
        $signature = rawurlencode($hash);

        if($request->pay_with_balance == false) {
            $msg = "Customer buying using payment";
            $data['fb_trx_id'] = $fbTransactionId;
            $data['ssl_trx_id'] = $sslTrxId;
            $data['app_url'] = config('facebookupsell.redirect_link') 
                . "/upsell-payment"
                . "?mobile={$msisdn}"
                . "&ssl_trx_id={$sslTrxId}"
                . "&fb_trx_id={$fbTransactionId}"
                . "&product_code={$productCode}"
                . "&product_price={$productPrice}"
                . "&product_validity={$productValidity}"
                . "&product_display_title_en={$productDisplayTitle}"
                . "&signature={$signature}"
                . "&timestamp={$timestamp}";
            
            return $this->apiBaseService->sendSuccessResponse($data, $msg, [], [], HttpStatusCode::SUCCESS);
        }

        $msg = "Only payment with balance in available for now";
        return $this->apiBaseService->sendErrorResponse($msg, [], HttpStatusCode::VALIDATION_ERROR);
    } 
}

/**
  * CURRENCY JOURNEY
  * 
  * 1. Find Customer By Phone No
  * 2. Check if customer is eligible for the product ** ERROR
  * 3. get product cost
  * 4. Redirect Payment Page
  */