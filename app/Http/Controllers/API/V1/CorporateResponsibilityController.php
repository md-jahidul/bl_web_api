<?php

namespace App\Http\Controllers\API\V1;

use App\Services\CorpCrStrategyComponentService;
use App\Services\CorporateRespSectionService;
use App\Services\MediaLandingPageService;
use App\Http\Controllers\Controller;
use App\Services\MediaPressNewsEventService;
use App\Services\MediaTvcVideoService;
use Illuminate\Http\JsonResponse;

class CorporateResponsibilityController extends Controller
{
    /**
     * @var CorporateRespSectionService
     */
    private $corporateRespSectionService;
    /**
     * @var CorpCrStrategyComponentService
     */
    private $corpCrStrategyComponentService;

    /**
     * CorporateRespSectionController constructor.
     * @param CorporateRespSectionService $corporateRespSectionService
     * @param CorpCrStrategyComponentService $corpCrStrategyComponentService
     */
    public function __construct(
        CorporateRespSectionService $corporateRespSectionService,
        CorpCrStrategyComponentService $corpCrStrategyComponentService
    ) {
        $this->corporateRespSectionService = $corporateRespSectionService;
        $this->corpCrStrategyComponentService = $corpCrStrategyComponentService;
    }

    public function getSection()
    {
        return $this->corporateRespSectionService->sections();
    }

    public function getCrStrategySection()
    {
        return $this->corpCrStrategyComponentService->crStrategySection();
    }
}
