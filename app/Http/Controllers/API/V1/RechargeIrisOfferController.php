<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Services\Banglalink\RechargeIrisOfferService;
use Illuminate\Http\Request;

class RechargeIrisOfferController extends Controller
{
    protected $service;

    /**
     * RechargeIrisOfferController constructor.
     * @param RechargeIrisOfferService $service
     */
    public function __construct(RechargeIrisOfferService $service)
    {
        $this->service = $service;
    }

    /**
     * @return bool
     */
    public function getRechargeIrisOffers(Request $request)
    {
        $msisdn = $request->msisdn;
        return $this->service->getIrisOffer($msisdn);
    }
}
