<?php

/**
 * Created by PhpStorm.
 * User: bs-205
 * Date: 18/08/19
 * Time: 17:23
 */

namespace App\Services;

use App\Repositories\ComponentRepository;
use App\Repositories\MediaBannerImageRepository;
use App\Repositories\MediaNewsCategoryRepository;
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
     * @var MediaNewsCategoryRepository
     */
    private $mediaNewsCategoryRepository;

    /**
     * DigitalServicesService constructor.
     * @param MediaPressNewsEventRepository $mediaPNERepository
     * @param MediaBannerImageRepository $mediaBannerImageRepository
     */
    public function __construct(
        MediaPressNewsEventRepository $mediaPNERepository,
        MediaBannerImageRepository $mediaBannerImageRepository,
        ComponentRepository $componentRepository,
        MediaNewsCategoryRepository $mediaNewsCategoryRepository
    ) {
        $this->mediaPNERepository = $mediaPNERepository;
        $this->mediaBannerImageRepository = $mediaBannerImageRepository;
        $this->componentRepository = $componentRepository;
        $this->mediaNewsCategoryRepository = $mediaNewsCategoryRepository;
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
        return $this->sendSuccessResponse($data, "$message Filter Data");
    }

    public function detailsComponent($urlSlug)
    {
        $post = $this->mediaPNERepository->getDataBySlug($urlSlug);
        $blogDetails = [];
        if (!empty($post->id)) {
            $blogDetails =  $this->componentRepository->getComponentByPageType('blog', $post->id);
        }
        return $this->sendSuccessResponse($blogDetails, "Blog details component");
    }

    public function filterArchive($type, $param, $limit)
    {
        $data = $this->mediaPNERepository->filterArchive($type, $param, $limit);
        return $this->sendSuccessResponse($data, "Filter Date");
    }

    public function topicList()
    {
        return $this->sendSuccessResponse($this->mediaNewsCategoryRepository->findAll(), "Topic List");
    }
}
