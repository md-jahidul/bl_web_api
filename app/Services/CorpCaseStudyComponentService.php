<?php

/**
 * Created by PhpStorm.
 * User: bs-205
 * Date: 18/08/19
 * Time: 17:23
 */

namespace App\Services;

use App\Repositories\AlFaqRepository;
use App\Repositories\CorpCaseStudyComponentRepository;
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
     * @var CorpCaseStudyComponentRepository
     */
    private $corpCaseStudyComponentRepository;

    /**
     * DigitalServicesService constructor.
     * @param CorpCaseStudySectionRepository $corpCaseStudySectionRepository
     * @param CorpCaseStudyComponentRepository $corpCaseStudyComponentRepository
     */
    public function __construct(
        CorpCaseStudySectionRepository $corpCaseStudySectionRepository,
        CorpCaseStudyComponentRepository $corpCaseStudyComponentRepository
    ) {
        $this->corpCaseStudySectionRepository = $corpCaseStudySectionRepository;
        $this->corpCaseStudyComponentRepository = $corpCaseStudyComponentRepository;
        $this->setActionRepository($corpCaseStudySectionRepository);
    }

    public function caseStudySectionWithComponent()
    {
        $sections = $this->corpCaseStudySectionRepository->getSections();

        $data = [];
        foreach ($sections as $section){
            if ($section->section_type == "left_image_right_text"){
                $data[] = [
                    'id' => $section->id,
                    'section_type' => $section->section_type,
                    'components' => isset($section->components[0]) ? $section->components[0] : json_decode("{}")
                ];
            } else {
                $data[] = [
                    'id' => $section->id,
                    'section_type' => $section->section_type,
                    'components' => $section->components
                ];
            }
        }

        return $this->sendSuccessResponse($data, 'Corporate CR Strategy Data!');
    }

    public function getComponentWithDetails($urlSlug)
    {
        $data = $this->corpCaseStudyComponentRepository->componentWithDetails($urlSlug);
        ($data) ? $data : $data = json_decode("{}");
        return $this->sendSuccessResponse($data, 'Corporate CR Strategy Details Components Data!');
    }
}
