<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UsageHistoryRequest;
use App\Services\Banglalink\CustomerRoamingUsageService;
use App\Services\Banglalink\CustomerSmsUsageService;
use Illuminate\Http\Request;

class RoamingUsageHistoryController extends Controller
{
    protected $service;

    public function __construct(CustomerRoamingUsageService $roamingUsageService)
    {
        $this->roamingUsageService = $roamingUsageService;
    }

    public function getSummaryUsageHistory(UsageHistoryRequest $request)
    {
        return $this->roamingUsageService->getSummaryUsageHistory($request);
    }

    public function getSmsUsageHistory(UsageHistoryRequest $request)
    {
        return $this->roamingUsageService->getSmsUsageHistory($request);
    }

    public function getDataUsageHistory(UsageHistoryRequest $request)
    {
        return $this->roamingUsageService->getDataUsageHistory($request);
    }

    public function getCallUsageHistory(UsageHistoryRequest $request)
    {
        return $this->roamingUsageService->getCallUsageHistory($request);
    }
}
