<?php

/**
 * Created by PhpStorm.
 * User: bs-205
 * Date: 18/08/19
 * Time: 17:23
 */

namespace App\Services;

use App\Repositories\MediaBannerImageRepository;
use App\Repositories\MediaPressNewsEventRepository;

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
     * @var ImageFileViewerService
     */
    private $imageFileViewerService;
    /**
     * @var MediaLandingPageService
     */
    private $mediaLandingPageService;

    /**
     * DigitalServicesService constructor.
     * @param MediaPressNewsEventRepository $mediaPNERepository
     * @param MediaBannerImageRepository $mediaBannerImageRepository
     * @param ImageFileViewerService $imageFileViewerService
     * @param MediaLandingPageService $mediaLandingPageService
     */
    public function __construct(
        MediaPressNewsEventRepository $mediaPNERepository,
        MediaBannerImageRepository $mediaBannerImageRepository,
        ImageFileViewerService $imageFileViewerService,
        MediaLandingPageService $mediaLandingPageService
    ) {
        $this->mediaPNERepository = $mediaPNERepository;
        $this->mediaBannerImageRepository = $mediaBannerImageRepository;
        $this->imageFileViewerService = $imageFileViewerService;
        $this->mediaLandingPageService = $mediaLandingPageService;
    }

    public function mediaPressEventData($moduleType)
    {
        $pressReleases = $this->mediaPNERepository->getPressNewsEvent($moduleType);

        foreach ($pressReleases as $key => $pressRelease) {
            $pressReleases[$key] = $this->mediaLandingPageService->getPressNewsImgData($pressRelease);
        }

        $bannerImage = $this->mediaBannerImageRepository->bannerImage($moduleType);

        $bannerKey = config('filesystems.moduleType.MediaBannerImage');

        if($bannerImage) {
            $imgData = $this->imageFileViewerService->prepareImageData($bannerImage, $bannerKey);
            $bannerImage = array_merge($bannerImage->toArray(), $imgData);
            unset($bannerImage['banner_image_url'], $bannerImage['banner_mobile_view']);
        }

        $message = ucfirst(str_replace('_', ' ', $moduleType));
        $data = [
            "body_section" => $pressReleases,
            'banner_image' =>  $bannerImage
        ];

        return $this->sendSuccessResponse($data, "$message Data");
    }

    public function mediaPressEventFilterData($moduleType, $from, $to)
    {
        $data = $this->mediaPNERepository->filterByDate($moduleType, $from, $to);
        $message = ucfirst(str_replace('_', ' ', $moduleType));
        return $this->sendSuccessResponse($data,"$message Filter Data");
    }
}
