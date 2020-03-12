<?php

/**
 * User: Bulbul Mahmud Nito
 * Date: 24/02/2020
 */

namespace App\Services\Banglalink;

use App\Services\ApiBaseService;
use App\Repositories\BusinessCategoryRepository;
use App\Repositories\BusinessHomeBannerRepository;
use App\Repositories\BusinessNewsRepository;
use App\Repositories\BusinessFeaturesRepository;
use App\Repositories\BusinessOthersRepository;
use App\Repositories\BusinessPackageRepository;
use App\Repositories\BusinessInternetRepository;
use App\Repositories\BusinessSlidingSpeedRepository;
use Illuminate\Http\Response;

class BusinessHomeService {

    /**
     * @var $businessCatRepo
     * @var $businessBannerRepo
     * @var $businessNewsRepo
     * @var $businessFeaturesRepo
     * @var $businessOtherRepo
     * @var $businessPackageRepo
     * @var $businessInternetRepo
     * @var $speedRepo
     */
    protected $businessCatRepo;
    protected $businessBannerRepo;
    protected $businessNewsRepo;
    protected $businessFeaturesRepo;
    protected $businessOtherRepo;
    protected $businessPackageRepo;
    protected $businessInternetRepo;
    protected $speedRepo;
    public $responseFormatter;

    /**
     * BusinessHomeService constructor.
     * @param BusinessCategoryRepository $businessCatRepo
     * @param BusinessHomeBannerRepository $businessBannerRepo
     * @param BusinessNewsRepository $businessNewsRepo
     * @param BusinessFeaturesRepository $businessFeaturesRepo
     * @param BusinessOthersRepository $businessOtherRepo
     * @param BusinessPackageRepository $businessPackageRepo
     * @param BusinessInternetRepository $businessInternetRepo
     * @param BusinessSlidingSpeedRepository $speedRepo
     */
    public function __construct(
    ApiBaseService $responseFormatter, BusinessCategoryRepository $businessCatRepo, BusinessHomeBannerRepository $businessBannerRepo, BusinessNewsRepository $businessNewsRepo, BusinessFeaturesRepository $businessFeaturesRepo, BusinessOthersRepository $businessOtherRepo, BusinessPackageRepository $businessPackageRepo, BusinessInternetRepository $businessInternetRepo, BusinessSlidingSpeedRepository $speedRepo
    ) {
        $this->businessCatRepo = $businessCatRepo;
        $this->businessBannerRepo = $businessBannerRepo;
        $this->businessNewsRepo = $businessNewsRepo;
        $this->businessFeaturesRepo = $businessFeaturesRepo;
        $this->businessOtherRepo = $businessOtherRepo;
        $this->businessPackageRepo = $businessPackageRepo;
        $this->businessInternetRepo = $businessInternetRepo;
        $this->speedRepo = $speedRepo;

        $this->responseFormatter = $responseFormatter;
    }

    /**
     * Get business product categories
     * @return Response
     */
    public function getHomeData() {
        $data = [];
        $data['categories'] = $this->businessCatRepo->getHomeCategoryList();
        $data['sliding_speed'] = $this->speedRepo->getSpeeds();
        $data['top_banners'] = $this->businessBannerRepo->getHomeBanners();
        $data['enterprise_solutions'] = $this->businessOtherRepo->getHomeOtherService();

        $homeShow = 1; //home show is true
        $data['packages'] = $this->businessPackageRepo->getPackageList($homeShow);

        $data['news'] = $this->businessNewsRepo->getNews();
        $data['internet'] = $this->businessInternetRepo->getInternetPackageList($homeShow);

        return $this->responseFormatter->sendSuccessResponse($data, 'Business Home Page Data');
    }

    /**
     * Get business categories
     * @return Response
     */
    public function getCategories() {
        $response = $this->businessCatRepo->getCategoryList();
        return $this->responseFormatter->sendSuccessResponse($response, 'Business Category List');
    }

}
