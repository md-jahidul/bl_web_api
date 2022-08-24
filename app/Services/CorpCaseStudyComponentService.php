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
use App\Repositories\CorpCaseStudyDetailsBannerRepository;
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
     * @var ImageFileViewerService
     */
    private $fileViewerService;
    /**
     * @var CorpCaseStudyDetailsBannerRepository
     */
    private $bannerRepository;

    /**
     * DigitalServicesService constructor.
     * @param CorpCaseStudySectionRepository $corpCaseStudySectionRepository
     * @param CorpCaseStudyComponentRepository $corpCaseStudyComponentRepository
     * @param CorpCaseStudyDetailsBannerRepository $bannerRepository
     * @param CorpRespContactUsRepository $contactUsRepository
     * @param ImageFileViewerService $fileViewerService
     */
    public function __construct(
        CorpCaseStudySectionRepository $corpCaseStudySectionRepository,
        CorpCaseStudyComponentRepository $corpCaseStudyComponentRepository,
        CorpCaseStudyDetailsBannerRepository $bannerRepository,
        CorpRespContactUsRepository $contactUsRepository,
        ImageFileViewerService $fileViewerService
    ) {
        $this->corpCaseStudySectionRepository = $corpCaseStudySectionRepository;
        $this->corpCaseStudyComponentRepository = $corpCaseStudyComponentRepository;
        $this->bannerRepository = $bannerRepository;
        $this->contactUsRepository = $contactUsRepository;
        $this->fileViewerService = $fileViewerService;
        $this->setActionRepository($corpCaseStudySectionRepository);
    }

    public function caseStudySectionWithComponent()
    {
        $sections = $this->corpCaseStudySectionRepository->getSections();
        $components = [];
        foreach ($sections as $section){

            $componentData = collect($section->components)->map(function ($data){
                $keyData = config('filesystems.moduleType.CorpCaseStudyComponent');
                $fileViewer = $this->fileViewerService->prepareImageData($data, $keyData);
                return [
                    'id' => $data->id,
                    'section_id' => $data->section_id,
                    'title_en' => $data->title_en,
                    'title_bn' => $data->title_bn,
                    'details_en' => $data->details_en,
                    'details_bn' => $data->details_bn,
                    'other_attributes' => $data->other_attributes,
                    'image_url_en' => isset($fileViewer["image_url_en"]) ? $fileViewer["image_url_en"] : null,
                    'image_url_bn' => isset($fileViewer['image_url_bn']) ? $fileViewer['image_url_bn'] : null,
                    'alt_text_en' => $data->alt_text_en,
                    'alt_text_bn' => $data->alt_text_bn,
                    'url_slug_en' => $data->url_slug_en,
                    'url_slug_bn' => $data->url_slug_bn,
                    'page_header' => $data->page_header,
                    'page_header_bn' => $data->page_header_bn
                ];
            });

            if ($section->section_type == "left_image_right_text"){
//                dd($componentData);
                $components[] = [
                    'id' => $section->id,
                    'section_type' => $section->section_type,
                    'components' => !empty($componentData) ? $componentData[0] : json_decode("{}")
                ];
            } else {
                $components[] = [
                    'id' => $section->id,
                    'section_type' => $section->section_type,
                    'components' => $componentData
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

        $banner = $this->bannerRepository->findOneByProperties(['details_id' => $components->id]);
        $keyData = config('filesystems.moduleType.CorpCaseStudyDetailsBanner');
        $fileViewer = $this->fileViewerService->prepareImageData($banner, $keyData);

        $bannerImg = [
            'alt_text_en' => ($banner->alt_text_en) ?? null,
            'alt_text_bn' => ($banner->alt_text_bn) ?? null,
            'banner_image_web_en' => isset($fileViewer["banner_image_web_en"]) ? $fileViewer["banner_image_web_en"] : null,
            'banner_image_web_bn' => isset($fileViewer['banner_image_web_bn']) ? $fileViewer['banner_image_web_bn'] : null,
            'banner_image_mobile_en' => isset($fileViewer["banner_image_mobile_en"]) ? $fileViewer["banner_image_mobile_en"] : null,
            'banner_image_mobile_bn' => isset($fileViewer['banner_image_mobile_bn']) ? $fileViewer['banner_image_mobile_bn'] : null
        ];

        $componentDetails = collect($components->components)->map(function ($data){
            $keyData = config('filesystems.moduleType.CorpCaseStudyComponentDetails');
            $fileViewer = $this->fileViewerService->prepareImageData($data, $keyData);
            return [
                'id' => $data->id,
                'section_details_id' => $data->section_id,
                'page_type' => $data->page_type,
                'component_type' => $data->component_type,
                'title_en' => $data->title_en,
                'title_bn' => $data->title_bn,
                'editor_en' => $data->editor_en,
                'editor_bn' => $data->editor_bn,
                'extra_title_en' => $data->extra_title_en,
                'extra_title_bn' => $data->extra_title_bn,
                'multiple_attributes' => $data->multiple_attributes,
                'image_url_en' => isset($fileViewer["image_url_en"]) ? $fileViewer["image_url_en"] : null,
                'image_url_bn' => isset($fileViewer['image_url_bn']) ? $fileViewer['image_url_bn'] : null,
                'video' => $data->video,
                'alt_text_en' => $data->alt_text,
                'alt_text_bn' => $data->alt_text_bn,
                'other_attributes' => $data->url_slug_en,
            ];
        });

        $components = [
            'id' => $components->id,
            'title_en' => $components->title_en,
            'title_bn' => $components->title_bn,
            'details_en' => $components->details_en,
            'details_bn' => $components->details_bn,
            'url_slug_en' => $components->url_slug_en,
            'url_slug_bn' => $components->url_slug_bn,
            'banner' => $bannerImg,
            'components' => $componentDetails
        ];

        ($components) ? $components : $components = json_decode("{}");
        $contactUsInfo = $this->contactUsRepository->getContactContent('case_study_and_report_details');
        $data = [
            'components' => $components,
            'contact_us' => $contactUsInfo
        ];
        return $this->sendSuccessResponse($data, 'Corporate CR Strategy Details Components Data!');
    }
}
