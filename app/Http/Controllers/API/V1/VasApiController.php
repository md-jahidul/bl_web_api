<?php

namespace App\Http\Controllers\API\V1;

use App\Exceptions\IdpAuthException;
use App\Http\Controllers\Controller;
use App\Services\Banglalink\AmarOfferService;
use App\Services\Banglalink\VasApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use DB;

class VasApiController extends Controller
{
    /**
     * @var AmarOfferService
     */
    protected $vasApiService;

    /**
     * AmarOfferController constructor.
     * @param VasApiService $vasApiService
     */
    public function __construct(VasApiService $vasApiService)
    {
        $this->vasApiService = $vasApiService;
    }

    /**
     * @param Request $request
     * @return JsonResponse|mixed
     */
    public function checkSubStatus(Request $request)
    {
        return $this->vasApiService->checkSubStatus($request->all());
    }

    /**
     * @param Request $request
     * @return JsonResponse|mixed
     */
    public function subscription(Request $request)
    {
        return $this->vasApiService->subscribe($request->all());
    }


}
