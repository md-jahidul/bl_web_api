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
        // $customer = $this->customerService->getCustomerDetails($request);
        // $connectionType = $customerInfo['balance_data']['connection_type'];
       // return $this->loyaltyService->getPriyojonStatus($customer->msisdn, "PREPAID");

        return $this->loyaltyService->getPriyojonStatus("01987", "PREPAID");
    }

    public function redeemOptions(Request $request)
    {
//        $customer = $this->customerService->getCustomerDetails($request);

//        dd($customer);

        return $this->loyaltyService->getRedeemOptions(1903303978);
    }

    public function partnerCatWithOffers()
    {
        return $this->loyaltyService->partnerOffers(1903303978);
    }


}
