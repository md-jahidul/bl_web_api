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
use App\Repositories\CorpRespContactUsRepository;
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
     * @var CorpRespContactUsRepository
     */
    private $contactUsRepository;

    /**
     * DigitalServicesService constructor.
     * @param CorpCaseStudySectionRepository $corpCaseStudySectionRepository
     * @param CorpCaseStudyComponentRepository $corpCaseStudyComponentRepository
     * @param CorpRespContactUsRepository $contactUsRepository
     */
    public function __construct(
        CorpCaseStudySectionRepository $corpCaseStudySectionRepository,
        CorpCaseStudyComponentRepository $corpCaseStudyComponentRepository,
        CorpRespContactUsRepository $contactUsRepository
    ) {
        $this->corpCaseStudySectionRepository = $corpCaseStudySectionRepository;
        $this->corpCaseStudyComponentRepository = $corpCaseStudyComponentRepository;
        $this->contactUsRepository = $contactUsRepository;
        $this->setActionRepository($corpCaseStudySectionRepository);
    }

    public function caseStudySectionWithComponent()
    {
        $sections = $this->corpCaseStudySectionRepository->getSections();
        $components = [];
        foreach ($sections as $section){
            if ($section->section_type == "left_image_right_text"){
                $components[] = [
                    'id' => $section->id,
                    'section_type' => $section->section_type,
                    'components' => isset($section->components[0]) ? $section->components[0] : json_decode("{}")
                ];
            } else {
                $components[] = [
                    'id' => $section->id,
                    'section_type' => $section->section_type,
                    'components' => $section->components
                ];
            }
        }
        $contactUsInfo = $this->contactUsRepository->getContactContent('case_study_and_report');
        $data = [
            'components' => $components,
            'contact_us' => $contactUsInfo
        ];

        return $this->sendSuccessResponse($data, 'Corporate CR Strategy Data!');
    }

    public function getComponentWithDetails($urlSlug)
    {
        $components = $this->corpCaseStudyComponentRepository->componentWithDetails($urlSlug);
        ($components) ? $components : $components = json_decode("{}");
        $contactUsInfo = $this->contactUsRepository->getContactContent('case_study_and_report_details');
        $data = [
            'components' => $components,
            'contact_us' => $contactUsInfo
        ];
        return $this->sendSuccessResponse($data, 'Corporate CR Strategy Details Components Data!');
    }
}
