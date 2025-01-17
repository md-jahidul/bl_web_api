<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Services\Banglalink\BusinessHomeService;
use App\Services\Banglalink\BusinessPackageService;
use App\Services\Banglalink\BusinessInternetService;
use App\Services\Banglalink\BusinessOthersService;
use Illuminate\Http\Request;
use DB;

class BusinessController extends Controller
{
    /**
     * @var BusinessHomeService
     * @var BusinessPackageService
     * @var BusinessInternetService
     * @var BusinessOthersService
     */
    protected $homeService;
    protected $packageService;
    protected $internetService;
    protected $enterpriseService;

    /**
     * BusinessController constructor.
     * @param BusinessHomeService $homeService
     * @param BusinessPackageService $packageService
     * @param BusinessInternetService $internetService
     * @param BusinessOthersService $enterpriseService
     */
    public function __construct(BusinessHomeService $homeService, BusinessPackageService $packageService, BusinessInternetService $internetService, BusinessOthersService $enterpriseService)
    {
        $this->homeService = $homeService;
        $this->packageService = $packageService;
        $this->internetService = $internetService;
        $this->enterpriseService = $enterpriseService;
    }

    /**
     * Get json data for home page
     * 
     * @param No
     * @return Json Response
     * @Bulbul Mahmud Nito || 24/02/2020
     */
    public function index()
    {
        return $this->homeService->getHomeData();
    }

    /**
     * Get category list
     * 
     * @param No
     * @return Json Response
     * @Bulbul Mahmud Nito || 24/02/2020
     */
    public function getCategories()
    {
        return $this->homeService->getCategories();
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
    public function packageBySlug($packageSlug)
    {
        return $this->packageService->getPackageBySlug($packageSlug);
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
    public function internetDetails($internetSlug)
    {
        return $this->internetService->getInternetDetails($internetSlug);
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
    public function enterpriseProductDetails($serviceSlug)
    {
        return $this->enterpriseService->getServiceBySlug($serviceSlug);
    }

}
