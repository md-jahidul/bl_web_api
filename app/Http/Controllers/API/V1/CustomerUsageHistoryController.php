<?php

namespace App\Http\Controllers\API\V1;

use App\Exceptions\IdpAuthException;
use App\Http\Controllers\Controller;
use App\Http\Requests\UsageHistoryRequest;
use App\Services\AboutUsService;
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
     * AboutUsController constructor.
     * @param AboutUsService $aboutUsService
     */
    public function __construct(CustomerSummaryUsageService $summaryUsageService)
    {
        $this->summaryUsageService = $summaryUsageService;
    }

    public function getSummaryHistory(UsageHistoryRequest $request)
    {
        return $this->summaryUsageService->getSummaryUsageHistory($request);
    }
}
