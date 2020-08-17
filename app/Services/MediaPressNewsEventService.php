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

    private const MODULE_TYPE = "press_news_event";

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

    public function pressReleaseData()
    {
        $pressRelease = $this->mediaPNERepository->getPressNewsEvent('press_release');
        $bannerImage = $this->mediaBannerImageRepository->bannerImage(self::MODULE_TYPE);

        $data = [
            "body_section" => $pressRelease,
            'banner_image' => $bannerImage
        ];

        return $this->sendSuccessResponse($data, 'Press Release Data');
    }

    public function pressReleaseFilterData($from, $to)
    {
        $data = $this->mediaPNERepository->filterByDate($from, $to);
        return $this->sendSuccessResponse($data, 'Press Release Data');
    }
}
