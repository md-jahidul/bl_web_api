<?php

/**
 * Created by PhpStorm.
 * User: bs-205
 * Date: 18/08/19
 * Time: 17:23
 */

namespace App\Services;

use App\Repositories\AlFaqRepository;
use App\Repositories\MediaBannerImageRepository;
use App\Repositories\MediaPressNewsEventRepository;
use App\Repositories\MediaTvcVideoRepository;
use App\Traits\CrudTrait;
use App\Traits\FileTrait;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class MediaTvcVideoService extends ApiBaseService
{
    /**
     * @var $sliderRepository
     */
    protected $mediaTvcVideoRepository;
    /**
     * @var MediaBannerImageRepository
     */
    private $mediaBannerImageRepository;

    private const MODULE_TYPE = "tvc_video";

    /**
     * MediaTvcVideoService constructor.
     * @param MediaTvcVideoRepository $mediaTvcVideoRepository
     * @param MediaBannerImageRepository $mediaBannerImageRepository
     */
    public function __construct(
        MediaTvcVideoRepository $mediaTvcVideoRepository,
        MediaBannerImageRepository $mediaBannerImageRepository
    ) {
        $this->mediaTvcVideoRepository = $mediaTvcVideoRepository;
        $this->mediaBannerImageRepository = $mediaBannerImageRepository;
    }

    public function getTvcVideoData()
    {
        $tvcVideos = $this->mediaTvcVideoRepository->getVideoItems();
        $bannerImage = $this->mediaBannerImageRepository->bannerImage(self::MODULE_TYPE);
        $data = [
            "body_section" => $tvcVideos,
            'banner_image' => $bannerImage
        ];

        return  $this->sendSuccessResponse($data, 'TVC Video Data');
    }
}
