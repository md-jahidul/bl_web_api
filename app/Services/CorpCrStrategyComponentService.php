<?php

/**
 * Created by PhpStorm.
 * User: bs-205
 * Date: 18/08/19
 * Time: 17:23
 */

namespace App\Services;

use App\Repositories\CorpCrStrategyComponentRepository;
use App\Repositories\CorporateCrStrategySectionRepository;
use App\Repositories\CorpRespContactUsRepository;
use App\Traits\CrudTrait;
use App\Traits\FileTrait;

class CorpCrStrategyComponentService extends ApiBaseService
{
    use CrudTrait;
    use FileTrait;

    /**
     * @var CorpCrStrategyComponentRepository
     */
    private $corpCrStrategyComponentRepo;
    /**
     * @var CorporateCrStrategySectionRepository
     */
    private $corpCrStrategySectionRepo;
    /**
     * @var CorpRespContactUsRepository
     */
    private $contactUsRepository;
    /**
     * @var ImageFileViewerService
     */
    private $fileViewerService;

    /**
     * DigitalServicesService constructor.
     * @param CorpCrStrategyComponentRepository $corpCrStrategyComponentRepository
     * @param CorporateCrStrategySectionRepository $corporateCrStrategySectionRepository
     * @param ImageFileViewerService $fileViewerService
     */
    public function __construct(
        CorpCrStrategyComponentRepository $corpCrStrategyComponentRepository,
        CorporateCrStrategySectionRepository $corporateCrStrategySectionRepository,
        ImageFileViewerService $fileViewerService
    ) {
        $this->corpCrStrategyComponentRepo = $corpCrStrategyComponentRepository;
        $this->corpCrStrategySectionRepo = $corporateCrStrategySectionRepository;
        $this->fileViewerService = $fileViewerService;
        $this->setActionRepository($corpCrStrategyComponentRepository);
    }

    public function crStrategySection()
    {
        $sections = $this->corpCrStrategySectionRepo->getSections();
        return $this->sendSuccessResponse($sections, 'Corporate CR Strategy Data!');
    }

    public function getComponentWithDetails($urlSlug)
    {
        $components = $this->corpCrStrategyComponentRepo->componentWithDetails($urlSlug);

        $collection = collect($components['components'])->map(function ($name, $key) {
            $multiImage = collect($name->multiple_attributes)->map(function ($data) {
                $keyData = config('filesystems.moduleType.CorpCrStrategyDetailsComponent');
                dd($data);
                $fileViewer = $this->fileViewerService->prepareImageData($data, $keyData);
                return [
                    "alt_text_bn" => $data['alt_text_en'],
                    "alt_text_en" => $data['alt_text_bn'],
                    "image_url_en" => $fileViewer["image_url_en"],
                    "image_url_bn" => $fileViewer['image_url_bn'],
                ];
            });

            return [
                "id" => $name->id,
                "section_details_id" => $name->section_details_id,
                "page_type" => $name->page_type,
                "component_type" => $name->component_type,
                "title_en" => $name->title_en,
                "title_bn" => $name->title_bn,
                "editor_en" => $name->editor_en,
                "editor_bn" => $name->editor_bn,
                "extra_title_bn" => $name->extra_title_bn,
                "extra_title_en" => $name->extra_title_en,
                "video" => $name->video,
                "image" => $name->image,
                "alt_text" => $name->alt_text,
                "other_attributes" => $name->other_attributes,
                'multiple_attributes' => $multiImage
            ];
        });

        unset($components['components']);
        $components['components'] = $collection;

        ($components) ? $components : $data = json_decode("{}");
        return $this->sendSuccessResponse($components, 'Corporate CR Strategy Details Components Data!');
    }
}
