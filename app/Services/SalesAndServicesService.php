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

        $results["component"] = "ServiceCenter";
        $results['title_en'] = $serviceCenterItems->title_en ?? null;
        $results['title_bn'] = $serviceCenterItems->title_bn ?? null;
        $results['description_en'] = $serviceCenterItems->description_en ?? null;
        $results['description_bn'] = $serviceCenterItems->description_bn ?? null;

        if( !empty($serviceCenterItems->other_attributes) && count($serviceCenterItems->other_attributes) > 0 ){
            $results['button'] = $serviceCenterItems->other_attributes;
        }
        else{
            $results['buttons'] = null;
        }



        return $results;

    }

    /**
     * [getSearchResults description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function getSearchResults($data)
    {

        $district = !empty($data['district']) ? trim($data['district']) : null;
        $thana = !empty($data['thana']) ? trim($data['thana']) : null;

        if( !empty($district) && !empty($thana) ){
            $serviceCenterItems = $this->salesAndServicesRepository->getServiceCenterByDistrictThana($district, $thana);
            $serviceCenterItems = SalesAndServicesResource::collection($serviceCenterItems);
            return $this->apiBaseService->sendSuccessResponse($serviceCenterItems, 'Data Found');
        }
        elseif( !empty($district) ){
            $serviceCenterItems = $this->salesAndServicesRepository->getServiceCenterByDistrict($district);
            $serviceCenterItems = SalesAndServicesResource::collection($serviceCenterItems);
            return $this->apiBaseService->sendSuccessResponse($serviceCenterItems, 'Data Found');
        }

    }


    public function getDistricts()
    {
        $districts = $this->salesAndServicesRepository->getAllDistrict();

        if( !empty($districts) && count($districts) > 0 ){
            $districts_arr = $districts->toArray();

            $districts_filter = array_map(function($value){
                return array_values($value)[0];
            }, $districts_arr);

            return $this->apiBaseService->sendSuccessResponse($districts_filter, 'Data Found');
        }
        else{
            return $this->apiBaseService->sendErrorResponse('Data Not Found');
        }

        
    }

    /**
     * [getThanaByDistricts description]
     * @return [type] [description]
     */
    public function getThanaByDistricts($data)
    {
        $district = trim($data['district']);

        $serviceCenterThana = $this->salesAndServicesRepository->getServiceCenterThanaByDistrict($district);

        if( !empty($serviceCenterThana) && count($serviceCenterThana) > 0 ){
            $thana_arr = $serviceCenterThana->toArray();

            $thana_arr_filter = array_map(function($value){
                return array_values($value)[0];
            }, $thana_arr);

            return $this->apiBaseService->sendSuccessResponse($thana_arr_filter, 'Data Found');
        }
        else{
            return $this->apiBaseService->sendErrorResponse('Data Not Found');
        }

    }


}
