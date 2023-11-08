<?php

namespace App\Http\Controllers\API\V1;

use App\Exceptions\IdpAuthException;
use App\Http\Controllers\Controller;
use App\Http\Requests\UsageHistoryRequest;
use App\Services\AboutUsService;
use App\Services\Banglalink\CustomerCallUsageService;
use App\Services\Banglalink\CustomerInternetUsageService;
use App\Services\Banglalink\CustomerRechargeHistoryService;
use App\Services\Banglalink\CustomerSmsUsageService;
use App\Services\Banglalink\CustomerSubscriptionUsageService;
use App\Services\Banglalink\CustomerSummaryUsageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class CustomerUsageHistoryController extends Controller
{
    /**
     * @var CustomerSummaryUsageService
     */
    private $summaryUsageService;
    /**
     * @var CustomerCallUsageService
     */
    private $callUsageService;
    /**
     * @var CustomerSmsUsageService
     */
    private $smsUsageService;
    /**
     * @var CustomerInternetUsageService
     */
    private $internetUsageService;
    /**
     * @var CustomerSubscriptionUsageService
     */
    private $subscriptionUsageService;
    /**
     * @var CustomerRechargeHistoryService
     */
    private $rechargeHistoryService;


    /**
     * AboutUsController constructor.
     * @param CustomerSummaryUsageService $summaryUsageService
     * @param CustomerCallUsageService $callUsageService
     * @param CustomerSmsUsageService $smsUsageService
     * @param CustomerInternetUsageService $internetUsageService
     * @param CustomerSubscriptionUsageService $subscriptionUsageService
     * @param CustomerRechargeHistoryService $rechargeHistoryService
     */
    public function __construct(
        CustomerSummaryUsageService $summaryUsageService,
        CustomerCallUsageService $callUsageService,
        CustomerSmsUsageService $smsUsageService,
        CustomerInternetUsageService $internetUsageService,
        CustomerSubscriptionUsageService $subscriptionUsageService,
        CustomerRechargeHistoryService $rechargeHistoryService
    ) {
        $this->summaryUsageService = $summaryUsageService;
        $this->callUsageService = $callUsageService;
        $this->smsUsageService = $smsUsageService;
        $this->internetUsageService = $internetUsageService;
        $this->subscriptionUsageService = $subscriptionUsageService;
        $this->rechargeHistoryService = $rechargeHistoryService;
    }

    public function getSummaryHistory(UsageHistoryRequest $request)
    {
        return $this->summaryUsageService->getSummaryUsageHistory($request);
    }

    public function getCallUsageHistory(UsageHistoryRequest $request)
    {
        return $this->callUsageService->getCallUsageHistory($request);
    }

    public function getSmsUsageHistory(UsageHistoryRequest $request)
    {
        return $this->smsUsageService->getSmsUsageHistory($request);
    }

    public function getInternetUsageHistory(UsageHistoryRequest $request)
    {
        return $this->internetUsageService->getInternetUsageHistory($request);
    }

    public function getSubscriptionUsageHistory(UsageHistoryRequest $request)
    {
        return $this->subscriptionUsageService->getSubscriptionUsageHistory($request);
    }

    public function getRechargeHistory(UsageHistoryRequest $request)
    {
        return $this->rechargeHistoryService->getRechargeHistory($request);
    }
}
