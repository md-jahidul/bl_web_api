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

        $collection = collect($sections)->map(function ($name, $key) {
            // This is for multi image
            $multiImage = collect($name->components)->map(function ($data){

                $keyData = config('filesystems.moduleType.CorpCrStrategyComponent');
                $fileViewer = $this->fileViewerService->prepareImageData($data, $keyData);

//                dd($fileViewer);

                return [
                    "id" => $data->id,
                    "section_id" =>$data->section_id,
                    "title_en" => $data->title_en,
                    "title_bn" => $data->title_bn,
                    "details_en" => $data->details_en,
                    "image_name_en" => $data->image_name_en,
                    "image_name_bn" => $data->image_name_bn,
                    "other_attributes" => $data->other_attributes,
                    "url_slug_en" => $data->url_slug_en,
                    "url_slug_bn" => $data->url_slug_bn,
                    "page_header" => $data->page_header,
                    "page_header_bn" => $data->page_header_bn,
                    "schema_markup" => $data->schema_markup,
                    "alt_text_bn" => $data['alt_text_en'],
                    "alt_text_en" => $data['alt_text_bn'],
                    "image_url_en" => isset($fileViewer["image_url_en"]) ? $fileViewer["image_url_en"] :  $data->image_base_url,
                    "image_url_bn" => isset($fileViewer['image_url_bn']) ? $fileViewer['image_url_bn'] :  $data->image_base_url,
                ];
            });


            return [
                "id" => $name->id,
                "section_type" => $name->section_type,
                "title_en" => $name->title_en,
                "title_bn" => $name->title_bn,
                'components' => $multiImage
            ];
        });

//        dd($collection);

        return $this->sendSuccessResponse($collection, 'Corporate CR Strategy Data!');
    }

    public function getComponentWithDetails($urlSlug)
    {
        $components = $this->corpCrStrategyComponentRepo->componentWithDetails($urlSlug);
        $collection = collect($components['components'])->map(function ($name, $key) {

            $keyData = config('filesystems.moduleType.CorpCrStrategyDetailsComponent');
            $fileViewer = $this->fileViewerService->prepareImageData($name, $keyData);

            // This is for multi image
            $multiImage = collect($name->componentMultiData)->map(function ($data){
                $keyData = config('filesystems.moduleType.CorpCrStrategyDetailsComponentMultiImg');
                $fileViewer = $this->fileViewerService->prepareImageData($data, $keyData);
//                dd($fileViewer);
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
                "image_url_en" => $fileViewer["image_url_en"],
                "image_url_bn" => $fileViewer['image_url_bn'],
                "alt_text" => $name->alt_text,
                "alt_text_bn" => $name->alt_text_bn,
                "other_attributes" => $name->other_attributes,
                'multiple_attributes' => $multiImage
            ];
        });

        unset($components['components']);
        $components['components'] = $collection;

        $keyData = config('filesystems.moduleType.CorpCrStrategyDetailsComponentBanner');
        $bannerImg = $this->fileViewerService->prepareImageData($components, $keyData);
        $components->banner = array_merge($components->banner, $bannerImg);
        unset($components['banner_image_web']);
        unset($components['banner_image_mobile']);
        unset($components['banner_name_en']);
        unset($components['banner_name_bn']);

        ($components) ? $components : $data = json_decode("{}");
        return $this->sendSuccessResponse($components, 'Corporate CR Strategy Details Components Data!');
    }
}
