<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Services\Banglalink\BusinessHomeService;
use App\Services\Banglalink\BusinessPackageService;
use App\Services\Banglalink\BusinessInternetService;
use App\Services\Banglalink\BusinessOthersService;
use App\Services\BusinessService;
use Illuminate\Http\Request;
use DB;
use Illuminate\Http\Response;

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
     * @var BusinessService
     */
    private $businessService;

    /**
     * BusinessController constructor.
     * @param BusinessHomeService $homeService
     * @param BusinessPackageService $packageService
     * @param BusinessInternetService $internetService
     * @param BusinessOthersService $enterpriseService
     */
    public function __construct(
        BusinessService $businessService,
        BusinessHomeService $homeService,
        BusinessPackageService $packageService,
        BusinessInternetService $internetService,
        BusinessOthersService $enterpriseService
    ) {
        $this->businessService = $businessService;
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

    public function getBusinessDataBySlug($slug)
    {
        return $this->businessService->getBusinessBySlug($slug);
    }

    public function getBusinessDetailsBySlug($slug, $urlSlug)
    {
        return $this->businessService->getBusinessDetailsBySlug($slug, $urlSlug);
    }

    /**
     * Give like and get total likes
     *
     * @param No
     * @return Response Response
     * @Bulbul Mahmud Nito || 15/03/2020
     */
    public function internetLike($internetId): Response
    {
        return $this->internetService->saveInternetLike($internetId);
    }
}
