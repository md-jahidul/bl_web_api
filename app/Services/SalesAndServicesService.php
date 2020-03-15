<?php

namespace App\Services;

use App\Http\Resources\SalesAndServicesResource;
use App\Repositories\SalesAndServicesRepository;
use App\Repositories\SearchRepository;
use App\Traits\CrudTrait;

class SalesAndServicesService
{
    use CrudTrait;

    /**
     * @var SalesAndServicesRepository
     */
    protected $salesAndServicesRepository;

    /**
     * @var ApiBaseService
     */
    protected $apiBaseService;

    /**
     * QuickLaunchService constructor.
     * @param SalesAndServicesRepository $salesAndServicesRepository
     * @param ApiBaseService $apiBaseService
     */
    public function __construct(SalesAndServicesRepository $salesAndServicesRepository, ApiBaseService $apiBaseService)
    {
        $this->salesAndServicesRepository = $salesAndServicesRepository;
        $this->apiBaseService = $apiBaseService;
        $this->setActionRepository($salesAndServicesRepository);
    }

    /**
     * @param $type
     * @return mixed
     */
    public function itemList()
    {        
        $serviceCenterItems = $this->salesAndServicesRepository->getServiceCenterByDistrict('Dhaka');
        return $serviceCenterItems = SalesAndServicesResource::collection($serviceCenterItems);
    }
}
