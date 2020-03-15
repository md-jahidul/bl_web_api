<?php

namespace App\Services;

use App\Http\Resources\QuickLaunchResource;
use App\Repositories\QuickLaunchRepository;
use App\Repositories\SearchRepository;
use App\Traits\CrudTrait;

class SalesAndServicesService
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
     * QuickLaunchService constructor.
     * @param QuickLaunchRepository $quickLaunchRepository
     * @param ApiBaseService $apiBaseService
     */
    public function __construct(QuickLaunchRepository $quickLaunchRepository, ApiBaseService $apiBaseService)
    {
        $this->quickLaunchRepository = $quickLaunchRepository;
        $this->apiBaseService = $apiBaseService;
        $this->setActionRepository($quickLaunchRepository);
    }

    /**
     * @param $type
     * @return mixed
     */
    public function itemList($type)
    {
        $quickLaunchItems = $this->quickLaunchRepository->getQuickLaunch($type);
        $quickLaunchItems = QuickLaunchResource::collection($quickLaunchItems);
        return $this->apiBaseService->sendSuccessResponse($quickLaunchItems, 'Data Found');
    }
}
