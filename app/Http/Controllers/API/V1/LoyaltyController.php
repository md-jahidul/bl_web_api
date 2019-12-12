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
     * LoyaltyController constructor.
     * @param LoyaltyService $loyaltyService
     */
    public function __construct(LoyaltyService $loyaltyService, CustomerService $customerService)
    {
        $this->loyaltyService = $loyaltyService;
        $this->customerService = $customerService;
    }

    public function priyojonStatus(Request $request)
    {
        $customer = $this->customerService->getCustomerDetails($request);

        return $this->loyaltyService->getPriyojonStatus($customer->phone);
    }


}
