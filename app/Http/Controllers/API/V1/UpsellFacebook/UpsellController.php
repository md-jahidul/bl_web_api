<?php

namespace App\Http\Controllers\API\V1\UpsellFacebook;

use App\Exceptions\IdpAuthException;
use App\Http\Controllers\Controller;
use App\Services\AboutUsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class UpsellController extends Controller
{
    public function phaseOne()
    {
        // OTP

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
         
        

    }

    public function phaseTwo()
    {
        
    }
}
