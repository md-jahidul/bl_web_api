<?php

namespace App\Http\Controllers\API\V1;

use App\Services\FixedPageMetaTagService;
use App\Services\MediaLandingPageService;
use App\Http\Controllers\Controller;
use App\Services\MediaPressNewsEventService;
use App\Services\MediaTvcVideoService;
use Illuminate\Http\JsonResponse;

class FixedPageMateTagController extends Controller
{
    /**
     * @var FixedPageMetaTagService
     */
    private $fixedPageMetaTagService;

    /**
     * RolesController constructor.
     * @param FixedPageMetaTagService $fixedPageMetaTagService
     */
    public function __construct(
        FixedPageMetaTagService $fixedPageMetaTagService
    ) {
        $this->fixedPageMetaTagService = $fixedPageMetaTagService;
    }

    public function getFixedMateTag($dynamicRouteKey)
    {
        return $this->fixedPageMetaTagService->metaTag($dynamicRouteKey);
    }
}
