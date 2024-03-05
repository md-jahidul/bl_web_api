<?php

namespace App\Http\Controllers\API\V1;

use App\Exceptions\IdpAuthException;
use App\Http\Controllers\Controller;
use App\Http\Requests\BuyAmarOfferRequest;
use App\Services\Banglalink\AmarOfferService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use DB;

class AmarOfferController extends Controller
{
    /**
     * @var AmarOfferService
     */
    protected $amarOfferService;

    /**
     * AmarOfferController constructor.
     * @param AmarOfferService $amarOfferService
     */
    public function __construct(AmarOfferService $amarOfferService)
    {
        $this->amarOfferService = $amarOfferService;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws IdpAuthException
     */
    public function getAmarOfferList(Request $request)
    {
        return $this->amarOfferService->getAmarOfferList($request);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws IdpAuthException
     */
    public function getAmarOfferListV2(Request $request)
    {
        return $this->amarOfferService->getAmarOfferListV2($request);
    }

    public function buyAmarOffer(BuyAmarOfferRequest $request)
    {
        return $this->amarOfferService->buyAmarOffer($request);
    }

    public function buyAmarOfferV2(BuyAmarOfferRequest $request)
    {
        return $this->amarOfferService->buyAmarOfferV2($request);
    }

    public function amarOfferDetails(Request $request, $offerType, $offerId)
    {
        return $this->amarOfferService->getDetailsV2($request, $offerType, $offerId);
    }

    
    /**
     * Check Recycle MSISDN
     * 
     * @param int $msisdn
     * @return JsonResponse
     */
    public function recycleMsisdnCheck($msisdn)
    {
        return $this->amarOfferService->checkRecycleMsisdn($msisdn);
    }
}
