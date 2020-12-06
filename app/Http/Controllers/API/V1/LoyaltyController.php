<?php
/**
 * Created by PhpStorm.
 * User: jahangir
 * Date: 12/9/19
 * Time: 12:24 PM
 */

namespace App\Http\Controllers\API\V1;


use App\Http\Controllers\Controller;
use App\Services\CustomerService;
use App\Services\LoyaltyService;
use App\Services\UserService;
use Illuminate\Http\Request;

class LoyaltyController extends Controller
{
    /**
     * @var LoyaltyService
     */
    protected $loyaltyService;

    /**
     * @var CustomerService
     */
    protected $customerService;
    /**
     * @var UserService
     */
    private $userService;

    /**
     * LoyaltyController constructor.
     * @param LoyaltyService $loyaltyService
     * @param CustomerService $customerService
     * @param UserService $userService
     */
    public function __construct(LoyaltyService $loyaltyService, CustomerService $customerService, UserService $userService)
    {
        $this->loyaltyService = $loyaltyService;
        $this->customerService = $customerService;
        $this->userService = $userService;
    }

    public function priyojonStatus(Request $request)
    {
        $customer = $this->customerService->getCustomerDetails($request);
        return $this->loyaltyService->getPriyojonStatus($customer->msisdn);
    }

    public function redeemOptions(Request $request)
    {
        $customer = $this->customerService->getCustomerDetails($request);
        $mobile = substr($customer->phone, 1);
        return $this->loyaltyService->getRedeemOffers($mobile); // 1962424630
    }

    public function redeemOfferPurchase(Request $request, $offerId)
    {
        $customer = $this->customerService->getCustomerDetails($request);
        return $this->loyaltyService->purchaseRedeemOffer($customer, $offerId);
    }

    public function partnerCatWithOffers(Request $request)
    {
        $customer = $this->customerService->getCustomerDetails($request);
        return $this->loyaltyService->partnerOffers($customer->phone);
    }

    public function partnerOfferLike($offerId)
    {
        return $this->loyaltyService->partnerOfferLike($offerId);
    }

}
