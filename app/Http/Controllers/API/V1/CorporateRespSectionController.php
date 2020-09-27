<?php

namespace App\Http\Controllers\API\V1;

use App\Services\CorporateRespSectionService;
use App\Services\MediaLandingPageService;
use App\Http\Controllers\Controller;
use App\Services\MediaPressNewsEventService;
use App\Services\MediaTvcVideoService;
use Illuminate\Http\JsonResponse;

class CorporateRespSectionController extends Controller
{
    /**
     * @var CorporateRespSectionService
     */
    private $corporateRespSectionService;

    /**
     * CorporateRespSectionController constructor.
     * @param CorporateRespSectionService $corporateRespSectionService
     */
    public function __construct(
        CorporateRespSectionService $corporateRespSectionService
    ) {
        $this->corporateRespSectionService = $corporateRespSectionService;
    }

    public function getSection()
    {
        return $this->corporateRespSectionService->sections();
    }
}
