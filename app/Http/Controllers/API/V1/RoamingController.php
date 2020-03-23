<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Services\Banglalink\RoamingService;
use Illuminate\Http\Request;
use DB;

class RoamingController extends Controller
{
    /**
     * @var $roammingService
     */
    protected $roammingService;
    /**
     * BusinessController constructor.
     * @param RoamingService $roammingService
     */
    public function __construct(RoamingService $roammingService)
    {
        $this->roammingService = $roammingService;
    }


    /**
     * Get category list
     * 
     * @param No
     * @return Json Response
     * @Bulbul Mahmud Nito || 23/03/2020
     */
    public function getCategories()
    {
        return $this->roammingService->getCategories();
    }

    /**
     * Get package category page data
     * 
     * @param No
     * @return Json Response
     * @Bulbul Mahmud Nito || 24/02/2020
     */
    public function packages()
    {
        return $this->packageService->getPackages();
    }

    /**
     * Get package details
     * 
     * @param No
     * @return Json Response
     * @Bulbul Mahmud Nito || 24/02/2020
     */
    public function packageById($packageId)
    {
        return $this->packageService->getPackageById($packageId);
    }

    /**
     * Get Internet package
     * 
     * @param No
     * @return Json Response
     * @Bulbul Mahmud Nito || 24/02/2020
     */
    public function internet()
    {
        return $this->internetService->getInternetPackage();
    }
    
    /**
     * Get Internet package details
     * 
     * @param $internetId
     * @return Json Response
     * @Bulbul Mahmud Nito || 15/03/2020
     */
    public function internetDetails($internetId)
    {
        return $this->internetService->getInternetDetails($internetId);
    }
    /**
     * Give like and get total likes
     * 
     * @param No
     * @return Json Response
     * @Bulbul Mahmud Nito || 15/03/2020
     */
    public function internetLike($internetId)
    {
        return $this->internetService->saveInternetLike($internetId);
    }

    /**
     * Get Enterprise Solution
     * 
     * @param $type (business-solusion,iot,others)
     * @return Json Response
     * @Bulbul Mahmud Nito || 24/02/2020
     */
    public function enterpriseSolusion($type)
    {
        return $this->enterpriseService->getOtherService($type);
    }
    
    /**
     * Get Enterprise Solution
     * 
     * @param $serviceId
     * @return Json Response
     * @Bulbul Mahmud Nito || 24/02/2020
     */
    public function enterpriseProductDetails($serviceId)
    {
        return $this->enterpriseService->getServiceById($serviceId);
    }

}
