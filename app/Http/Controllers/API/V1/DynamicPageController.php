<?php

namespace App\Http\Controllers\API\V1;

use App\Services\Assetlite\ComponentService;
use App\Services\DynamicPageService;
use App\Http\Controllers\Controller;


class DynamicPageController extends Controller
{

    /**
     * @var DynamicPageService
     */
    private $pageService;

    /**
     * @var ComponentService
     */
    private $componentService;

    protected const PAGE_TYPE = "other_dynamic_page";

    /**
     * DynamicPageController constructor.
     * @param DynamicPageService $pageService
     * @param ComponentService $componentService
     */
    public function __construct(DynamicPageService $pageService, ComponentService $componentService)
    {
        $this->pageService = $pageService;
        $this->componentService = $componentService;
    }

    public function getDynamicPageData($slug)
    {
        return $this->pageService->pageData($slug);
    }

}
