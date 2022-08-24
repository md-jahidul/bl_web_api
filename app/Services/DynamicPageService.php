<?php

/**
 * Created by PhpStorm.
 * User: BS23
 * Date: 27-Aug-19
 * Time: 3:56 PM
 */

namespace App\Services;

use App\Repositories\ComponentRepository;
use App\Repositories\DynamicPageRepository;
use App\Repositories\FooterMenuRepository;
use App\Services\Assetlite\ComponentService;
use App\Traits\CrudTrait;
use App\Traits\FileTrait;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;

class DynamicPageService extends ApiBaseService
{
    use CrudTrait;
    use FileTrait;

    /**
     * @var $prizeService
     */
    protected $pageRepo;

    protected $componentRepository;

    protected $footerMenuRepository;

    protected const PageType = "other_dynamic_page";
    /**
     * @var ImageFileViewerService
     */
    private $fileViewerService;

    /**
     * DynamicPageService constructor.
     * @param DynamicPageRepository $pageRepo
     * @param ComponentRepository $componentRepository
     * @param FooterMenuRepository $footerMenuRepository
     * @param ImageFileViewerService $fileViewerService
     */
    public function __construct(
        DynamicPageRepository $pageRepo,
        ComponentRepository $componentRepository,
        FooterMenuRepository $footerMenuRepository,
        ImageFileViewerService $fileViewerService
    ) {
        $this->pageRepo = $pageRepo;
        $this->componentRepository = $componentRepository;
        $this->footerMenuRepository = $footerMenuRepository;
        $this->fileViewerService = $fileViewerService;
        $this->setActionRepository($pageRepo);
    }


    public function pageData($slug)
    {
        $dynamicData = $this->pageRepo->page(strtolower($slug));
        $keyData = config('filesystems.moduleType.DynamicPage');
        $fileViewer = $this->fileViewerService->prepareImageData($dynamicData, $keyData);

        if (!empty($dynamicData)) {
            $componentDetails = collect($dynamicData->components)->map(function ($data){
                $keyData = config('filesystems.moduleType.DynamicPageComponent');
                $fileViewer = $this->fileViewerService->prepareImageData($data, $keyData);

                // This is for multi image
                $multiImage = collect($data->componentMultiData)->map(function ($data){
                    $keyData = config('filesystems.moduleType.DynamicPageComponentMultiImg');
                    $fileViewer = $this->fileViewerService->prepareImageData($data, $keyData);
                    return [
                        "alt_text_bn" => $data['alt_text_en'],
                        "alt_text_en" => $data['alt_text_bn'],
                        "image_url_en" => $fileViewer["image_url_en"],
                        "image_url_bn" => $fileViewer['image_url_bn'],
                    ];
                });

                return [
                    'id' => $data->id,
                    'section_details_id' => $data->section_details_id,
                    'page_type' => $data->page_type,
                    'component_type' => $data->component_type,
                    'title_en' => $data->title_en,
                    'title_bn' => $data->title_bn,
                    'editor_en' => $data->editor_en,
                    'editor_bn' => $data->editor_bn,
                    'extra_title_en' => $data->extra_title_en,
                    'extra_title_bn' => $data->extra_title_bn,
                    'video' => $data->video,
                    'image_url_en' => isset($fileViewer["image_url_en"]) ? $fileViewer["image_url_en"] : null,
                    'image_url_bn' => isset($fileViewer['image_url_bn']) ? $fileViewer['image_url_bn'] : null,
                    'alt_text' => $data->alt_text,
                    'alt_text_bn' => $data->alt_text_bn,
                    'other_attributes' => $data->other_attributes,
                    'multiple_attributes' => $multiImage
                ];
            });

            $pagePageInfo = [
                "id" => $dynamicData->id,
                "page_header" => $dynamicData->page_header,
                "page_header_bn" => $dynamicData->page_header_bn,
                "schema_markup" => $dynamicData->schema_markup,
                'banner_image_web_en' => isset($fileViewer["banner_image_web_en"]) ? $fileViewer["banner_image_web_en"] : null,
                'banner_image_web_bn' => isset($fileViewer['banner_image_web_bn']) ? $fileViewer['banner_image_web_bn'] : null,
                'banner_image_mobile_en' => isset($fileViewer["banner_image_mobile_en"]) ? $fileViewer["banner_image_mobile_en"] : null,
                'banner_image_mobile_bn' => isset($fileViewer['banner_image_mobile_bn']) ? $fileViewer['banner_image_mobile_bn'] : null,
                "alt_text_en" =>  $dynamicData->alt_text,
                "alt_text_bn" =>  $dynamicData->alt_text_bn,
                "page_name_en" => $dynamicData->page_name_en,
                "page_name_bn" => $dynamicData->page_name_bn,
                "page_content_en" => $dynamicData->page_content_en,
                "page_content_bn" => $dynamicData->page_content_bn,
                "url_slug" => $dynamicData->url_slug,
                "url_slug_bn" => $dynamicData->url_slug_bn,
                "components" => $componentDetails
            ];
        }else{
            $pagePageInfo = (object)[];
        }

        return $this->sendSuccessResponse($pagePageInfo, 'Dynamic page data');
    }
}
