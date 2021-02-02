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
     * @var ImageFileViewerService
     */
    private $imageFileViewerService;

    /**
     * MediaTvcVideoService constructor.
     * @param MediaTvcVideoRepository $mediaTvcVideoRepository
     * @param MediaBannerImageRepository $mediaBannerImageRepository
     * @param ImageFileViewerService $imageFileViewerService
     */
    public function __construct(
        MediaTvcVideoRepository $mediaTvcVideoRepository,
        MediaBannerImageRepository $mediaBannerImageRepository,
        ImageFileViewerService $imageFileViewerService
    ) {
        $this->mediaTvcVideoRepository = $mediaTvcVideoRepository;
        $this->mediaBannerImageRepository = $mediaBannerImageRepository;
        $this->imageFileViewerService = $imageFileViewerService;
    }

    public function getTvcVideoData()
    {
        $tvcVideos = $this->mediaTvcVideoRepository->getVideoItems();
        $bannerImage = $this->mediaBannerImageRepository->bannerImage(self::MODULE_TYPE);

        $bannerKey = config('filesystems.moduleType.MediaBannerImage');

        if($bannerImage) {
            $imgData = $this->imageFileViewerService->prepareImageData($bannerImage, $bannerKey);
            $bannerImage = array_merge($bannerImage->toArray(), $imgData);
            unset($bannerImage['banner_image_url'], $bannerImage['banner_mobile_view']);
        }

        $data = [
            "body_section" => $tvcVideos,
            'banner_image' => $bannerImage
        ];

        return  $this->sendSuccessResponse($data, 'TVC Video Data');
    }
}
