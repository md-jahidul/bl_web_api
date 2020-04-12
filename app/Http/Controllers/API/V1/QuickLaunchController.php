<?php

namespace App\Http\Controllers\API\V1;

use App\Services\QuickLaunchService;
use App\Http\Controllers\Controller;

class QuickLaunchController extends Controller
{

    /**
     * @var $quickLaunchService
     */
    private $quickLaunchService;

    /**
     * QuickLaunchController constructor.
     * @param QuickLaunchService $quickLaunchService
     */
    public function __construct(QuickLaunchService $quickLaunchService)
    {
        $this->quickLaunchService = $quickLaunchService;
    }

    /**
     * @return mixed
     */
    public function getQuickLaunchItems()
    {
        return $this->quickLaunchService->itemListButton('button');
    }

}
