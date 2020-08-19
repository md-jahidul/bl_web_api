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
     * DigitalServicesService constructor.
     * @param MediaPressNewsEventRepository $mediaPNERepository
     * @param MediaBannerImageRepository $mediaBannerImageRepository
     */
    public function __construct(
        MediaPressNewsEventRepository $mediaPNERepository,
        MediaBannerImageRepository $mediaBannerImageRepository
    ) {
        $this->mediaPNERepository = $mediaPNERepository;
        $this->mediaBannerImageRepository = $mediaBannerImageRepository;
    }

    public function mediaPressEventData($moduleType)
    {
        $pressRelease = $this->mediaPNERepository->getPressNewsEvent($moduleType);
        $bannerImage = $this->mediaBannerImageRepository->bannerImage($moduleType);

        $data = [
            "body_section" => $pressRelease,
            'banner_image' => $bannerImage
        ];
        return $this->sendSuccessResponse($data, 'Press Release Data');
    }

    public function mediaPressEventFilterData($moduleType, $from, $to)
    {
        $data = $this->mediaPNERepository->filterByDate($moduleType, $from, $to);
        return $this->sendSuccessResponse($data, 'Press Release Data');
    }

//    public function newsEventData()
//    {
//        $pressRelease = $this->mediaPNERepository->getPressNewsEvent('news_event');
//        $bannerImage = $this->mediaBannerImageRepository->bannerImage(self::MODULE_TYPE);
//        $data = [
//            "body_section" => $pressRelease,
//            'banner_image' => $bannerImage
//        ];
//        return $this->sendSuccessResponse($data, 'News Event Data');
//    }
//
//    public function newsEventFilterData($from, $to)
//    {
//        $data = $this->mediaPNERepository->filterByDate($from, $to);
//        return $this->sendSuccessResponse($data, 'News Event Filter Data');
//    }
}
