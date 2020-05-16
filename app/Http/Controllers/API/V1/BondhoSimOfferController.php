<?php

namespace App\Http\Controllers\API\V1;

use App\Exceptions\IdpAuthException;
use App\Http\Controllers\Controller;
use App\Http\Requests\BuyAmarOfferRequest;
use App\Services\Banglalink\AmarOfferService;
use App\Services\Banglalink\BondhoSimOfferService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use DB;

class BondhoSimOfferController extends Controller
{
    /**
     * @var BondhoSimOfferService
     */
    private $bondhoSimOfferService;

    /**
     * AmarOfferController constructor.
     * @param BondhoSimOfferService $bondhoSimOfferService
     */
    public function __construct(BondhoSimOfferService $bondhoSimOfferService)
    {
        $this->bondhoSimOfferService = $bondhoSimOfferService;
    }

    /**
     * @param $mobile
     * @return JsonResponse
     */
    public function getBondhoSimOfferCheck($mobile)
    {
        return $this->bondhoSimOfferService->getBondhoSimOfferResponse($mobile);
    }
}
