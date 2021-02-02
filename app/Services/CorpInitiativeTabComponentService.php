<?php

namespace App\Services;

use App\Repositories\CorpInitiativeTabComponentRepository;
use App\Repositories\CorporateInitiativeTabRepository;
use App\Traits\CrudTrait;
use App\Traits\FileTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class CorpInitiativeTabComponentService extends ApiBaseService
{
    use CrudTrait;
    use FileTrait;

    /**
     * @var CorpInitiativeTabComponentRepository
     */
    private $tabComponentRepository;
    /**
     * @var CorporateInitiativeTabRepository
     */
    private $initiativeTabRepository;
    /**
     * @var ImageFileViewerService
     */
    private $fileViewerService;

    /**
     * AppServiceProductService constructor.
     * @param CorporateInitiativeTabRepository $initiativeTabRepository
     * @param CorpInitiativeTabComponentRepository $tabComponentRepository
     * @param ImageFileViewerService $fileViewerService
     */
    public function __construct(
        CorporateInitiativeTabRepository $initiativeTabRepository,
        CorpInitiativeTabComponentRepository $tabComponentRepository,
        ImageFileViewerService $fileViewerService
    ) {
        $this->initiativeTabRepository = $initiativeTabRepository;
        $this->tabComponentRepository = $tabComponentRepository;
        $this->fileViewerService = $fileViewerService;
        $this->setActionRepository($tabComponentRepository);
    }


    /**
     * @param $slug
     * @return JsonResponse|mixed
     */
    public function getTabComponents($slug)
    {
        $tabId = $this->initiativeTabRepository->getTabInfo($slug);
        $tabId = isset($tabId) ? $tabId->id : null;
        $data = $this->tabComponentRepository->tabWiseComponent($tabId);

        $collection = collect($data)->map(function ($component, $key) use ($data) {
            if ($component['component_type'] == 'batch_component') {
                $multiImage = collect($component['batchTab'])->map(function ($batchCom) {
                    $batchTabComponent = collect($batchCom['batchTabComponents'])->map(function ($batchCom) {
                        $keyData = config('filesystems.moduleType.CorpIntBatchComponent');
                        $fileViewer = $this->fileViewerService->prepareImageData($batchCom, $keyData);
                        return [
                            'title_en' => $batchCom->title_en,
                            'title_bn' => $batchCom->title_bn,
                            'alt_text_en' => $batchCom->alt_text_en,
                            'alt_text_bn' => $batchCom->alt_text_bn,
                            'image_url_en' => $fileViewer["image_url_en"],
                            'image_url_bn' => $fileViewer['image_url_bn']
                        ];
                    });
                    return [
                        'tab_title_en' => $batchCom->title_en,
                        'tab_title_bn' => $batchCom->title_bn,
                        'data' => $batchTabComponent
                    ];
                });
            } else {

                if (isset($component->multiple_attributes)) {
                    $multiImage = $component->multiple_attributes;
                } else {
                    $multiImage = collect($component->multiComponent)->map(function ($multiData) use ($component) {
                        $keyData = config('filesystems.moduleType.CorpIntMultiComponent');
                        $fileViewer = $this->fileViewerService->prepareImageData($multiData, $keyData);
                        return [
                            "title_en" => $multiData['title_en'],
                            "title_bn" => $multiData['title_bn'],
                            "editor_en" => $multiData['editor_en'],
                            "editor_bn" => $multiData['editor_bn'],
                            "alt_text_en" => $multiData['alt_text_en'],
                            "alt_text_bn" => $multiData['alt_text_bn'],
                            "image_url_en" => $fileViewer["image_url_en"],
                            "image_url_bn" => $fileViewer['image_url_bn'],
                        ];
                    });
                }
            }

            $keyData = config('filesystems.moduleType.CorpIntTabComponent');
            $fileViewer = $this->fileViewerService->prepareImageData($component, $keyData);

            $parentCom = [
                "initiative_tab_id" => $component->initiative_tab_id,
                "component_type" => $component->component_type,
                "component_title_en" => $component->component_title_en,
                "component_title_bn" => $component->component_title_bn,
                'multiple_attributes' => $multiImage
            ];

            if ($component->component_type == 'news_component' || $component->component_type == 'young_future') {
                $singleCom = [
                    "editor_en" => $component->editor_en,
                    "editor_bn" => $component->editor_bn,
                    "image_url_en" => isset($fileViewer["image_url_en"]) ? $fileViewer["image_url_en"] : '',
                    "image_url_bn" => isset($fileViewer['image_url_bn']) ? $fileViewer['image_url_bn'] : '',
                    "alt_text_en" => $component['single_alt_text_en'],
                    "alt_text_bn" => $component['single_alt_text_bn'],
                ];
                $parentCom = array_merge($parentCom, $singleCom);
            }

            return $parentCom;
        });

        return $this->sendSuccessResponse($collection, "Corporate Initiative Tab Component");
    }

}
