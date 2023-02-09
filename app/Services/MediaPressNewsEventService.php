<?php

/**
 * Created by PhpStorm.
 * User: bs-205
 * Date: 18/08/19
 * Time: 17:23
 */

namespace App\Services;

use App\Repositories\AdTechRepository;
use App\Repositories\ComponentRepository;
use App\Repositories\MediaBannerImageRepository;
use App\Repositories\MediaPressNewsEventRepository;
use App\Services\Assetlite\ComponentService;

class MediaPressNewsEventService extends ApiBaseService
{
    /**
     * @var $sliderRepository
     */
    protected $mediaPNERepository;
    /**
     * @var MediaBannerImageRepository
     */
    private $mediaBannerImageRepository;
    /**
     * @var ComponentRepository
     */
    private $componentRepository;
    /**
     * @var AdTechRepository
     */
    private $adTechRepository;

    /**
     * DigitalServicesService constructor.
     * @param MediaPressNewsEventRepository $mediaPNERepository
     * @param MediaBannerImageRepository $mediaBannerImageRepository
     */
    public function __construct(
        MediaPressNewsEventRepository $mediaPNERepository,
        MediaBannerImageRepository $mediaBannerImageRepository,
        ComponentRepository $componentRepository,
        AdTechRepository $adTechRepository
    ) {
        $this->mediaPNERepository = $mediaPNERepository;
        $this->mediaBannerImageRepository = $mediaBannerImageRepository;
        $this->componentRepository = $componentRepository;
        $this->adTechRepository = $adTechRepository;
    }

    public function mediaPressEventData($moduleType)
    {
        $pressRelease = $this->mediaPNERepository->getPressNewsEvent($moduleType);
        $bannerImage = $this->mediaBannerImageRepository->bannerImage($moduleType);
        $message = ucfirst(str_replace('_', ' ', $moduleType));
        $data = [
            "body_section" => $pressRelease,
            'banner_image' => $bannerImage
        ];
        return $this->sendSuccessResponse($data, "$message Data");
    }

    public function mediaPressEventFilterData($moduleType, $from, $to)
    {
        $data = $this->mediaPNERepository->filterByDate($moduleType, $from, $to);
        $message = ucfirst(str_replace('_', ' ', $moduleType));
        return $this->sendSuccessResponse($data,"$message Filter Data");
    }

    public function detailsComponent($urlSlug)
    {
        $post = $this->mediaPNERepository->getDataBySlug($urlSlug);
        $components = [];
        if (!empty($post->id)) {
            $components =  $this->componentRepository->getComponentByPageType('blog', $post->id);
        }

        $blogDetails['ad_tech'] = $this->adTechRepository->findOneByProperties(['reference_type' => "blog"]);
        $blogDetails['components'] = $components;

        return $this->sendSuccessResponse($blogDetails, "Blog details component");
    }

    public function filterArchive($type,$param,$limit){
        $data = $this->mediaPNERepository->filterArchive($type,$param,$limit);
        return $this->sendSuccessResponse($data, "Filter Date");
    }
}
