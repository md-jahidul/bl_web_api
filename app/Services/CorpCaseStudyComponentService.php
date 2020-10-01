<?php

/**
 * Created by PhpStorm.
 * User: bs-205
 * Date: 18/08/19
 * Time: 17:23
 */

namespace App\Services;

use App\Repositories\AlFaqRepository;
use App\Repositories\CorpCaseStudySectionRepository;
use App\Repositories\CorpCrStrategyComponentRepository;
use App\Repositories\CorporateCrStrategySectionRepository;
use App\Repositories\MediaPressNewsEventRepository;
use App\Traits\CrudTrait;
use App\Traits\FileTrait;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class CorpCaseStudyComponentService extends ApiBaseService
{
    use CrudTrait;

    /**
     * @var CorpCaseStudySectionRepository
     */
    private $corpCaseStudySectionRepository;

    /**
     * DigitalServicesService constructor.
     * @param CorpCaseStudySectionRepository $corpCaseStudySectionRepository
     */
    public function __construct(
        CorpCaseStudySectionRepository $corpCaseStudySectionRepository
    ) {
        $this->corpCaseStudySectionRepository = $corpCaseStudySectionRepository;
        $this->setActionRepository($corpCaseStudySectionRepository);
    }

    public function caseStudySectionWithComponent()
    {
        $sections = $this->corpCaseStudySectionRepository->getSections();
        return $this->sendSuccessResponse($sections, 'Corporate CR Strategy Data!');
    }

    public function getComponentWithDetails($urlSlug)
    {
        $data = $this->corpCrStrategyComponentRepo->componentWithDetails($urlSlug);
        ($data) ? $data : $data = json_decode("{}");
        return $this->sendSuccessResponse($data, 'Corporate CR Strategy Details Components Data!');
    }
}
