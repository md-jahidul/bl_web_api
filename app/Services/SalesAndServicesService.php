<?php

namespace App\Services;

use App\Http\Resources\SalesAndServicesResource;
use App\Repositories\SalesAndServicesRepository;
use App\Repositories\SearchRepository;
use App\Traits\CrudTrait;
use App\Services\Assetlite\ComponentService;

class SalesAndServicesService
{
    use CrudTrait;

    /**
     * @var SalesAndServicesRepository
     */
    protected $salesAndServicesRepository;

    /**
     * [$componentService description]
     * @var [type]
     */
    protected $componentService;

    /**
     * @var ApiBaseService
     */
    protected $apiBaseService;

    /**
     * QuickLaunchService constructor.
     * @param SalesAndServicesRepository $salesAndServicesRepository
     * @param ApiBaseService $apiBaseService
     */
    public function __construct(SalesAndServicesRepository $salesAndServicesRepository, ApiBaseService $apiBaseService, ComponentService $componentService)
    {
        $this->salesAndServicesRepository = $salesAndServicesRepository;
        $this->apiBaseService = $apiBaseService;
        $this->componentService = $componentService;
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

    /**
     * [itemHeader description]
     * @return [type] [description]
     */
    public function itemHeader(){

        $serviceCenterItems = $this->componentService->findByType('home_sales_service_center');

        dd($serviceCenterItems);

    }

}
