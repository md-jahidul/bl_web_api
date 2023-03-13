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

    public function __construct(CustomerRoamingUsageService $service)
    {
        $this->service = $service;
       // $this->middleware('idp.verify');
    }

    public function getSummaryUsageHistory(UsageHistoryRequest $request)
    {
        return $this->service->getSummaryUsageHistory($request);
    }

    public function getSmsUsageHistory(UsageHistoryRequest $request)
    {
        return $this->service->getSmsUsageHistory($request);
    }

    public function getDataUsageHistory(UsageHistoryRequest $request)
    {
        return $this->service->getDataUsageHistory($request);
    }

    public function getCallUsageHistory(UsageHistoryRequest $request)
    {
        return $this->service->getCallUsageHistory($request);
    }
}
