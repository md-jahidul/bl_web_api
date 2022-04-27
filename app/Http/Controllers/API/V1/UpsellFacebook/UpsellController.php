<?php

namespace App\Http\Controllers\API\V1\UpsellFacebook;

use App\Enums\HttpStatusCode;
use App\Exceptions\IdpAuthException;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpsellPurchaseProductRequest;
use App\Http\Requests\UpsellRequestProductRequest;
use App\Services\AboutUsService;
use App\Services\ApiBaseService;
use App\Services\Banglalink\BalanceService;
use App\Services\CustomerService;
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
    public function requestPurchase(Request $request)
    {
        /**
         * OTP JOURNEY
         * 
         * 1. Find Customer By Phone No
         * 2. Check if customer is eligible for the product ** ERROR
         * 3. get product cost
         * 4. check customer balance
         * 5. 4 > 3 Return OTP page & Send OTP to Customer Phone
         * 6. 4 < 3 Return Primary Error
         */

        /**
         * CURRENCY JOURNEY
         * 
         * 1. Find Customer By Phone No
         * 2. Check if customer is eligible for the product ** ERROR
         * 3. get product cost
         * 4. Return Payment Page
         */

        $customer = $this->customerService->getCustomerInfoByPhone($request->input('msisdn'));
        $product = $this->productService->getProductByCode( $request->input('product_code'));
        $result = $this->productService->eligible($request->msisdn, $request->input('product_code'));
        $customerStatus =  $result->getData();
        $customerType = $customer->number_type;

        if($customerStatus->status_code != 200){
            $msg = "Customer is not eligible";

            //Redirect to ERROR PAGE with [error = TRUE, error_msg = $msg]
            $link = config('facebookupsell.redirect_link') 
            . "/upsell-error" 
            . "?msg={$msg}";    
            
            return redirect()->to($link);
        }

        $productPrice = $product->productCore->price;

        if($request->pay_with_balance) {
            $res = $this->upsellService->buyWithBalance($request->input('msisdn'), $customer, $productPrice, $customerType, $this->balanceService);
            
            if($res['status_code'] != 200) {
                $msg = "Could not send OTP";

                // Fallback OTP sending operation to ClientEnd
                // $link = config('facebookupsell.redirect_link') 
                // . "/upsell-otp"
                // . "?mobile={$request->input('msisdn')}"
                // . "&otp_token=NULL"
                // . "&validation_time=NULL"
                // . "&product_code={$product->product_code}";

                //Redirect to ERROR PAGE with [error = TRUE, error_msg = $msg]
                $link = config('facebookupsell.redirect_link') 
                . "/upsell-error" 
                . "?msg={$msg}";    
                
                return redirect()->to($link);
            }

            $validationTime = $res['data']['validation_time'];
            $otpToken = $res['data']['otp_token'];

            // Redirect to OTP VERIFICATION PAGE VIEW with
            $link = config('facebookupsell.redirect_link') 
            . "/upsell-otp"
            . "?mobile={$request->input('msisdn')}"
            . "&otp_token={$otpToken}"
            . "&validation_time={$validationTime}"
            . "&product_code={$product->product_code}";    
            
            return redirect()->to($link);
        }

        // Redirect to BANK / MOBILE PAYMENT VIEW with [customer no, product_code & detail, Cost]
    }

    /**
     * POST Request BODY
     * msisdn: string,
     * product_code: string
     */
    public function purchaseProduct(UpsellPurchaseProductRequest $request)
    {
        $msisdn      = "88" . $request->input('msisdn');
        $productCode = $request->input('product_code');
    
        $result = $this->upsellService->purchaseProduct($msisdn, $productCode);
    
        if ($result['status_code'] == 200) {
            $msg = "Purchase request successfully received and under process";
            return $this->apiBaseService->sendSuccessResponse(
                json_decode($result['response'], true), $msg, [], HttpStatusCode::SUCCESS);
        } else {
            $msg = "Purchase Failed";
            return $this->apiBaseService->sendSuccessResponse(
                json_decode($result['response'], true), $msg, [], HttpStatusCode::BAD_REQUEST);
        }     
    }
}

// return $this->apiBaseService->sendErrorResponse(
// json_decode($result['response'], true), $msg, [], HttpStatusCode::BAD_REQUEST);
