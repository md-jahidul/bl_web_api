<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
//use App\Http\Requests\AmarOfferDetailsRequest;
//use App\Http\Requests\BuyAmarOfferRequest;
//use App\Http\Requests\UsageHistoryRequest;
use App\Services\Banglalink\AmarOfferService;
//use App\Services\Banglalink\CustomerSmsUsageService;
use Illuminate\Http\Request;

class AmarOfferController extends Controller
{
    protected $service;

    public function __construct(AmarOfferService $service)
    {
        $this->service = $service;
//        $this->middleware('idp.verify');
    }

    public function getAmarOfferList(Request $request)
    {
        return $this->service->getAmarOfferList($request);
    }

//    public function buyAmarOffer(BuyAmarOfferRequest $request)
//    {
//        return $this->service->buyAmarOffer($request);
//    }
}
