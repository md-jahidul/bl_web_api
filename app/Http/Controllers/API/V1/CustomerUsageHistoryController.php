<?php

namespace App\Http\Controllers\API\V1;

use App\Exceptions\IdpAuthException;
use App\Http\Controllers\Controller;
use App\Http\Requests\UsageHistoryRequest;
use App\Services\AboutUsService;
use App\Services\Banglalink\CustomerCallUsageService;
use App\Services\Banglalink\CustomerSummaryUsageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class CustomerUsageHistoryController extends Controller
{

    /**
     * @var AboutUsService
     */
    protected $aboutUsService;
    /**
     * @var CustomerSummaryUsageService
     */
    private $summaryUsageService;
    /**
     * @var CustomerCallUsageService
     */
    private $callUsageService;


    /**
     * AboutUsController constructor.
     * @param AboutUsService $aboutUsService
     */
    public function __construct(
        CustomerSummaryUsageService $summaryUsageService,
        CustomerCallUsageService $callUsageService
    ) {
        $this->summaryUsageService = $summaryUsageService;
        $this->callUsageService = $callUsageService;
    }

    public function getSummaryHistory(UsageHistoryRequest $request)
    {
        return $this->summaryUsageService->getSummaryUsageHistory($request);
    }

    public function getCallUsageHistory(UsageHistoryRequest $request)
    {
        return $this->callUsageService->getCallUsageHistory($request);
    }
}
