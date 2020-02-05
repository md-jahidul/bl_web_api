<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\AmarOfferDetails;
//use App\Http\Requests\AmarOfferDetailsRequest;
//use App\Http\Requests\BuyAmarOfferRequest;
//use App\Http\Requests\UsageHistoryRequest;
use App\Services\Banglalink\AmarOfferService;
//use App\Services\Banglalink\CustomerSmsUsageService;
use Illuminate\Http\Request;
use DB;

class AmarOfferController extends Controller
{
    protected $service;
    private $offerDetails;

    public function __construct(AmarOfferService $service, AmarOfferDetails $offerDetails)
    {
        $this->service = $service;
        $this->offerDetails = $offerDetails;
//        $this->middleware('idp.verify');
    }

    public function getAmarOfferList(Request $request)
    {
        return $this->service->getAmarOfferList($request);
    }
    
    public function getAmarOfferDetails($type){
        $details = $this->offerDetails
                ->select(DB::raw('details_en, details_bn, CASE type WHEN 1 THEN "Internet" WHEN 2 THEN "Voice" ELSE "Bundle" END as type'))
                ->where('type', $type)->first();
        
        return $details;
    }

//    public function buyAmarOffer(BuyAmarOfferRequest $request)
//    {
//        return $this->service->buyAmarOffer($request);
//    }
}
