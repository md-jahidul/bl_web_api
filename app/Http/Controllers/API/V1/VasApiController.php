<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckSubStatusRequest;
use App\Http\Requests\VasCancelSubscription;
use App\Http\Requests\VasCheckSubStatusRequest;
use App\Http\Requests\VasSubscriptionRequest;
use App\Services\Banglalink\VasApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use DB;

class VasApiController extends Controller
{
    /**
     * @var VasApiService
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
     * @param VasCheckSubStatusRequest $request
     * @return JsonResponse|mixed
     */
    public function checkSubStatus(VasCheckSubStatusRequest $request)
    {
        return $this->vasApiService->checkSubStatus($request->all());
    }

    /**
     * @param VasSubscriptionRequest $request
     * @return JsonResponse|mixed
     */
    public function subscription(VasSubscriptionRequest $request)
    {
        return $this->vasApiService->subscribe($request->all());
    }

    /**
     * @param VasCancelSubscription $request
     * @return JsonResponse|mixed
     */
    public function cancelSubscription(VasCancelSubscription $request)
    {
        return $this->vasApiService->cancelSubscription($request->all());
    }

    /**
     * @param $providerUrl
     * @return JsonResponse|mixed
     */
    public function contentList($providerUrl)
    {
        return $this->vasApiService->contentList($providerUrl);
    }

    public function contentDetail($providerUrl, $contentId)
    {
        return $this->vasApiService->contentDetail($providerUrl, $contentId);
    }

}
