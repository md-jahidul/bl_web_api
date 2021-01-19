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
        $data['enterprise_solutions'] = $this->getHomeOtherService();

        $homeShow = 1; //home show is true
        $data['packages'] = $this->getPackageList($homeShow);

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

    public function getPackageList($homeShow = 0)
    {
        $packageData = $this->businessPackageRepo->getPackageList($homeShow);
        $packageKeyData = config('filesystems.moduleType.BusinessPackages');

        $data = [];
        $count = 0;

        foreach ($packageData as $package) {
            $imgData = $this->imageFileViewerService->prepareImageData($package, $packageKeyData);
            $data[$count] = array_merge($package->toArray(), $imgData);
            $data[$count]['slug'] = 'packages';
            $data[$count]['banner_alt_text'] = $package->card_banner_alt_text;
            $data[$count]['banner_alt_text_bn'] = $package->card_banner_alt_text_bn;
            unset($data[$count]['card_banner_web'], $data[$count]['card_banner_mobile'],
            $data[$count]['card_banner_name_en'], $data[$count]['card_banner_name_bn'],
            $data[$count]['card_banner_alt_text'],$data[$count]['card_banner_alt_text_bn']);

            $count++;
        }
        return $data;
    }

    public function getHomeOtherService()
    {
        $services = $this->businessOtherRepo->getHomeOtherService();
        $data = [];
        $countTop = 0;

        foreach ($services['servicesTop'] as $s) {
            $data['top'][$countTop]['id'] = $s->id;
            $data['top'][$countTop]['slug'] = $s->type;
            $data['top'][$countTop]['icon'] = $s->icon == "" ? "" : config('filesystems.image_host_url') . $s->icon;
            $data['top'][$countTop]['name_en'] = $s->name;
            $data['top'][$countTop]['name_bn'] = $s->name_bn;
            $data['top'][$countTop]['home_short_details_en'] = $s->home_short_details_en;
            $data['top'][$countTop]['home_short_details_bn'] = $s->home_short_details_bn;
            $data['top'][$countTop]['short_details_en'] = $s->short_details;
            $data['top'][$countTop]['short_details_bn'] = $s->short_details_bn;
            $data['top'][$countTop]['url_slug'] = $s->url_slug;
            $data['top'][$countTop]['url_slug_bn'] = $s->url_slug_bn;
            $countTop++;
        }

        $otherKeyData = config('filesystems.moduleType.BusinessOthers');
        $countSlider = 0;

        foreach ($services['servicesSlider'] as $s) {
            $imgData = $this->imageFileViewerService->prepareImageData($s, $otherKeyData);
            $data['slider'][$countSlider]['id'] = $s->id;
            $data['slider'][$countSlider]['slug'] = $s->type;
            $data['slider'][$countSlider]['alt_text'] = $s->alt_text;
            $data['slider'][$countSlider]['icon'] = config('filesystems.image_host_url') . $s->icon;
            $data['slider'][$countSlider]['name_en'] = $s->name;
            $data['slider'][$countSlider]['name_bn'] = $s->name_bn;
            $data['slider'][$countSlider]['home_short_details_en'] = $s->home_short_details_en;
            $data['slider'][$countSlider]['home_short_details_bn'] = $s->home_short_details_bn;
            $data['slider'][$countSlider]['short_details_en'] = $s->short_details;
            $data['slider'][$countSlider]['short_details_bn'] = $s->short_details_bn;
            $data['slider'][$countSlider]['url_slug'] = $s->url_slug;
            $data['slider'][$countSlider]['url_slug_bn'] = $s->url_slug_bn;
            $data['slider'][$countSlider] = array_merge($data['slider'][$countSlider], $imgData);
            $countSlider++;
        }
        return $data;
    }
    /**
     * Get business categories
     * @return Response
     */
    public function getCategories()
    {
        $categories = $this->businessCatRepo->getCategoryList();
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
        foreach ($categories as $v) {
            $data[$count]['id'] = $v->id;
            $data[$count]['slug'] = $slugs[$v->id];
            $data[$count]['name_en'] = $v->name;
            $data[$count]['name_bn'] = $v->name_bn;
            $data[$count]['banner_alt_text'] = $v->alt_text;
            $data[$count]['banner_alt_text_bn'] = $v->alt_text_bn;
            $imgData = $this->imageFileViewerService->prepareImageData($v, $catKeyData);
            $data[$count] = array_merge($data[$count], $imgData);
            $data[$count]['url_slug'] = $v->url_slug;
            $data[$count]['url_slug_bn'] = $v->url_slug_bn;
            $data[$count]['page_header'] = $v->page_header;
            $data[$count]['page_header_bn'] = $v->page_header;
            $data[$count]['schema_markup'] = $v->schema_markup;
            $count++;
        }
        
        return $this->responseFormatter->sendSuccessResponse($data, 'Business Category List');
    }

}
