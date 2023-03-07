<?php

namespace App\Http\Controllers\API\V1;

use App\Services\AlBannerService;
use App\Http\Controllers\Controller;


class EmergencyBalanceController extends Controller
{

    /**
     * @var AlBannerService
     */
    private $alBannerService;

    protected const PAGE_TYPE = "emergency_balance";

    /**
     * EmergencyBalanceController constructor.
     * @param AlBannerService $pageService
     */
    public function __construct(AlBannerService $alBannerService)
    {
        $this->alBannerService = $alBannerService;
    }

    public function emergencyBalancebanner()
    {
        $message = 'Emergency Balance page data';
        return $this->alBannerService->singlePagebanner(self::PAGE_TYPE, $message);
    }

}
