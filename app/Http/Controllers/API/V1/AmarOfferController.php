<?php

namespace App\Http\Controllers\API\V1;

use App\Exceptions\IdpAuthException;
use App\Http\Controllers\Controller;
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
     * @param $type
     * @return JsonResponse
     */
    public function getAmarOfferDetails($type)
    {
        return $this->amarOfferService->getAmarOfferDetails($type);
    }

//    public function buyAmarOffer(BuyAmarOfferRequest $request)
//    {
//        return $this->service->buyAmarOffer($request);
//    }
}
