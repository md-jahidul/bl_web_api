<?php

namespace App\Http\Controllers\API\V1\UpsellFacebook;

use App\Enums\HttpStatusCode;
use App\Exceptions\IdpAuthException;
use App\Http\Controllers\Controller;
use App\Http\Requests\AuthTokenRefreshRequest;
use App\Http\Requests\AuthTokenRequest;
use App\Http\Requests\UpsellPurchaseFinalizationRequest;
use App\Http\Requests\UpsellPurchaseInvocationRequest;
use App\Http\Requests\UpsellTokenRefreshRequest;
use App\Http\Requests\UpsellTokenRequest;
use App\Services\ApiBaseService;
use App\Services\Banglalink\BalanceService;
use App\Services\CustomerService;
use App\Services\IdpIntegrationService;
use App\Services\ProductService;
use App\Services\UpsellFacebook\UpsellService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


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

        $customer = $this->customerService->getCustomerInfoByPhone($request->input('msisdn'));
        $product = $this->productService->getProductByCode( $request->input('product_code')); 
        $eligibleCustomer = $this->productService->eligible($request->msisdn, $request->input('product_code'))->getData()->data->is_eligible;

        if(!$product || !$customer || !$eligibleCustomer) {
            $msg = "Customer is not eligible Or Invalid product";

            //Redirect to ERROR PAGE with [error = TRUE, error_msg = $msg]
            $link = config('facebookupsell.redirect_link') 
                . "/upsell-error" 
                . "?msg={$msg}";    
            
            return redirect()->to($link);
        }

        if(!$request->pay_with_balance) {
            // Redirect to PAYMENT PAGE VIEW with
            $link = config('facebookupsell.redirect_link') 
                . "/upsell-payment"
                . "?mobile={$request->input('msisdn')}"
                . "&product_code={$product->product_code}"
                . "&product_price={$product->productCore->price}";    
            
            return redirect()->to($link);
        }

        $otpToken = null;
        $validationTime = null;
        $res = $this->upsellService->buyWithBalance($request->input('msisdn'), $customer, $product->productCore->price, $customer->number_type, $this->balanceService);

        if(isset($res['data']['otp_token'])) {
            $otpToken = $res['data']['otp_token'];
        }

        if(isset($res['data']['validation_time'])) {
            $validationTime = $res['data']['validation_time'];
        }

        // Redirect to OTP VERIFICATION PAGE with
        $link = config('facebookupsell.redirect_link') 
            . "/upsell-otp"
            . "?mobile={$request->input('msisdn')}"
            . "&otp_token={$otpToken}"
            . "&validation_time={$validationTime}"
            . "&product_code={$product->product_code}"
            . "&product_price={$product->productCore->price}";    
        
        return redirect()->to($link);
    } 
    

    /**
     * POST Request BODY
     * msisdn: string,
     * product_code: string
     */
    public function purchaseProduct(UpsellPurchaseFinalizationRequest $request)
    {
        $msisdn      = "88" . $request->input('msisdn');
        $productCode = $request->input('product_code');
    
        $response = $this->upsellService->purchaseProduct($msisdn, $productCode);
        $responseData = json_decode($response['response'], true);

        if ($response['status_code'] == 200) {
            $msg = "Purchase request successfully received and under process";
            return $this->apiBaseService->sendSuccessResponse(
                json_decode($response['response'], true), $msg, [], HttpStatusCode::SUCCESS);
        } else {
            $msg = "Purchase Failed";
            return $this->apiBaseService->sendErrorResponse(
                $msg, $responseData['data']['errors'][0]['detail'], HttpStatusCode::BAD_REQUEST);
        }     
    }
}

