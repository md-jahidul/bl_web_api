<?php

namespace App\Http\Controllers\API\V1;

use App\Services\AlBannerService;
use App\Http\Controllers\Controller;


class AlBannerController extends Controller
{

    /**
     * @var AlBannerService
     */
    private $alBannerService;

    /**
     * @var ComponentService
     */
    private $componentService;

    protected const PAGE_TYPE = "emergency_balance";

    /**
     * AlBannerController constructor.
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
