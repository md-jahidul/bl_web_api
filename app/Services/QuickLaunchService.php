<?php

namespace App\Services;

use App\Http\Resources\QuickLaunchResource;
use App\Repositories\QuickLaunchRepository;
use App\Repositories\SearchRepository;
use App\Traits\CrudTrait;

class QuickLaunchService
{
    use CrudTrait;

    /**
     * @var QuickLaunchRepository
     */
    protected $quickLaunchRepository;

    /**
     * @var ApiBaseService
     */
    protected $apiBaseService;

    /**
     * @var ImageFileViewerService
     */
    private $imageFileViewerService;

    /**
     * QuickLaunchService constructor.
     * @param QuickLaunchRepository $quickLaunchRepository
     * @param ApiBaseService $apiBaseService
     * @param ImageFileViewerService $imageFileViewerService
     */
    public function __construct(
        QuickLaunchRepository $quickLaunchRepository,
        ApiBaseService $apiBaseService,
        ImageFileViewerService $imageFileViewerService
    ) {
        $this->quickLaunchRepository = $quickLaunchRepository;
        $this->apiBaseService = $apiBaseService;
        $this->setActionRepository($quickLaunchRepository);
        $this->imageFileViewerService = $imageFileViewerService;
    }

    /**
     * @param $type
     * @return mixed
     */
    public function itemList($type)
    {
        $quickLaunchItems = $this->quickLaunchRepository->getQuickLaunch($type);
        $keyData = config('filesystems.moduleType.QuickLaunch');

        foreach ($quickLaunchItems as $key => $item) {
            $imgData = $this->imageFileViewerService->prepareImageData($item, $keyData);

            $quickLaunchItems[$key] = (object) array_merge($item->toArray(), $imgData);
        }

        return  QuickLaunchResource::collection($quickLaunchItems);
    }

    public function itemListButton($type)
    {
        $quickLaunchItems = $this->quickLaunchRepository->getQuickLaunch($type);
        $quickLaunchItems = QuickLaunchResource::collection($quickLaunchItems);
        return $this->apiBaseService->sendSuccessResponse($quickLaunchItems, 'Data Found');
    }
}
