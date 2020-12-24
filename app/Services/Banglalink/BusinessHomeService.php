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
use App\Services\ImageFileViewerService;
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
    protected $imageFileViewerService;
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
     * @param ImageFileViewerService $imageFileViewerService
     */
    public function __construct(
        ApiBaseService $responseFormatter,
        BusinessCategoryRepository $businessCatRepo,
        BusinessHomeBannerRepository $businessBannerRepo,
        BusinessNewsRepository $businessNewsRepo,
        BusinessFeaturesRepository $businessFeaturesRepo,
        BusinessOthersRepository $businessOtherRepo,
        BusinessPackageRepository $businessPackageRepo,
        BusinessInternetRepository $businessInternetRepo,
        BusinessSlidingSpeedRepository $speedRepo,
        ImageFileViewerService $imageFileViewerService
    ) {
        $this->businessCatRepo = $businessCatRepo;
        $this->businessBannerRepo = $businessBannerRepo;
        $this->businessNewsRepo = $businessNewsRepo;
        $this->businessFeaturesRepo = $businessFeaturesRepo;
        $this->businessOtherRepo = $businessOtherRepo;
        $this->businessPackageRepo = $businessPackageRepo;
        $this->businessInternetRepo = $businessInternetRepo;
        $this->speedRepo = $speedRepo;
        $this->imageFileViewerService = $imageFileViewerService;

        $this->responseFormatter = $responseFormatter;
    }

    /**
     * Get business product categories
     * @return Response
     */
    public function getHomeData() {
        $data = [];
        $data['categories'] = $this->getHomeCategoryList();
        $data['sliding_speed'] = $this->speedRepo->getSpeeds();
        $data['top_banners'] = $this->getHomeBanners();
        $data['enterprise_solutions'] = $this->businessOtherRepo->getHomeOtherService();

        $homeShow = 1; //home show is true
        $data['packages'] = $this->businessPackageRepo->getPackageList($homeShow);

        $data['news'] = $this->getNews();
        $data['internet'] = $this->businessInternetRepo->getInternetPackageList($homeShow);

        return $this->responseFormatter->sendSuccessResponse($data, 'Business Home Page Data');
    }

    public function getHomeCategoryList()
    {
        $categories = $this->businessCatRepo->getHomeCategoryList();
        $catKeyData = config('filesystems.moduleType.BusinessCategory');

        $data = [];
        $count = 0;

        $slugs = array(
            1 => 'packages',
            2 => 'internet',
            3 => 'business-solution',
            4 => 'iot',
            5 => 'others',
        );

        foreach ($categories as $category) {
            $imgData = $this->imageFileViewerService->prepareImageData($category, $catKeyData);
            unset($category->banner_photo, $category->banner_image_mobile);

            $data[$count] = $category;
            $data[$count]['slug'] = $slugs[$category->id];
            $data[$count] = array_merge($data[$count]->toArray(), $imgData);

            $count++;
        }

        return $data;
    }

    public function getNews()
    {
        $news = $this->businessNewsRepo->getNews();
        $newsKeyData = config('filesystems.moduleType.BusinessNews');

        $data = [];
        $count = 0;

        foreach($news as $v) {
            $imgData = $this->imageFileViewerService->prepareImageData($v, $newsKeyData);

            $data['sliding_speed'] = $v->sliding_speed;
            $data['data'][$count] = $v;
            $data['data'][$count] = array_merge($data['data'][$count]->toArray(), $imgData);
            unset($data['data'][$count]['image_url'], $data['data'][$count]['sliding_speed']);

            $count++;
        }

        return $data;
    }

    public function getHomeBanners()
    {
        $banners = $this->businessBannerRepo->getHomeBanners();
        $bannerKeyData = config('filesystems.moduleType.BusinessHomeBanner');

        $data = [];
        $count = 0;
        foreach($banners as $banner){
            $imgData = $this->imageFileViewerService->prepareImageData($banner, $bannerKeyData);

            $data[$count] = $banner;
            $data[$count]['home_sort'] = $banner->home_sort == 1 ? 'left' : 'right';
            $data[$count] = array_merge($data[$count]->toArray(), $imgData);

            unset($data[$count]['image_name'], $data[$count]['image_name_mobile']);
            $count++;
        }

        return $data;
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
