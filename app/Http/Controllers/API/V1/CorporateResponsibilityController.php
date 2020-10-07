<?php

namespace App\Http\Controllers\API\V1;

use App\Services\CorpCaseStudyComponentService;
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
     * @var CorpCaseStudyComponentService
     */
    private $corpCaseStudyComponentService;

    /**
     * CorporateRespSectionController constructor.
     * @param CorporateRespSectionService $corporateRespSectionService
     * @param CorpCrStrategyComponentService $corpCrStrategyComponentService
     * @param CorpCaseStudyComponentService $corpCaseStudyComponentService
     */
    public function __construct(
        CorporateRespSectionService $corporateRespSectionService,
        CorpCrStrategyComponentService $corpCrStrategyComponentService,
        CorpCaseStudyComponentService $corpCaseStudyComponentService
    ) {
        $this->corporateRespSectionService = $corporateRespSectionService;
        $this->corpCrStrategyComponentService = $corpCrStrategyComponentService;
        $this->corpCaseStudyComponentService = $corpCaseStudyComponentService;
    }

    public function getSection()
    {
        return $this->corporateRespSectionService->sections();
    }

    /**
     * @return JsonResponse|mixed
     */
    public function getCrStrategySection()
    {
        return $this->corpCrStrategyComponentService->crStrategySection();
    }

    public function getCrStrategyDetailsComponents($urlSlug)
    {
        return $this->corpCrStrategyComponentService->getComponentWithDetails($urlSlug);
    }

    public function getCaseStudySection()
    {
        return $this->corpCaseStudyComponentService->caseStudySectionWithComponent();
    }

    public function getCaseStudyDetailsComponents($urlSlug)
    {
        return $this->corpCaseStudyComponentService->getComponentWithDetails($urlSlug);
    }
}
