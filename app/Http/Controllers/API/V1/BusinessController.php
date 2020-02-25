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
     * @return Factory|View
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
     * @return Factory|View
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
     * @return Factory|View
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
     * @return Factory|View
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
     * @return Factory|View
     * @Bulbul Mahmud Nito || 24/02/2020
     */
    public function internet()
    {
        return $this->internetService->getInternetPackage();
    }

    /**
     * Get Enterprise Solution
     * 
     * @param $type (business-solusion,iot,others)
     * @return Factory|View
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
     * @return Factory|View
     * @Bulbul Mahmud Nito || 24/02/2020
     */
    public function enterpriseProductDetails($serviceId)
    {
        return $this->enterpriseService->getServiceById($serviceId);
    }

}
