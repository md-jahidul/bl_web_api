<?php

/**
 * Created by PhpStorm.
 * User: bs-205
 * Date: 18/08/19
 * Time: 17:23
 */

namespace App\Services;

use App\Repositories\MediaBannerImageRepository;
use App\Repositories\MediaLandingPageRepository;
use App\Repositories\MediaPressNewsEventRepository;
use App\Repositories\MediaTvcVideoRepository;

class MediaLandingPageService extends ApiBaseService
{
    protected $mediaLandingPageRepository;
    protected $mediaTvcVideoRepository;
    protected $mediaPressNewsEventRepository;
    /**
     * @var MediaBannerImageRepository
     */
    private $mediaBannerImageRepository;

    /**
     * @var ImageFileViewerService
     *
     */
    private $imageFileViewerService;

    /**
     * DigitalServicesService constructor.
     * @param MediaLandingPageRepository $mediaLandingPageRepository
     * @param MediaTvcVideoRepository $mediaTvcVideoRepository
     * @param MediaPressNewsEventRepository $mediaPressNewsEventRepository
     * @param MediaBannerImageRepository $mediaBannerImageRepository
     * @param ImageFileViewerService $imageFileViewerService
     */
    public function __construct(
        MediaLandingPageRepository $mediaLandingPageRepository,
        MediaTvcVideoRepository $mediaTvcVideoRepository,
        MediaPressNewsEventRepository $mediaPressNewsEventRepository,
        MediaBannerImageRepository $mediaBannerImageRepository,
        ImageFileViewerService $imageFileViewerService
    ) {
        $this->mediaLandingPageRepository = $mediaLandingPageRepository;
        $this->mediaTvcVideoRepository = $mediaTvcVideoRepository;
        $this->mediaPressNewsEventRepository = $mediaPressNewsEventRepository;
        $this->mediaBannerImageRepository = $mediaBannerImageRepository;
        $this->imageFileViewerService = $imageFileViewerService;
    }

    public function getMediaFeatureData($componentsData, $type = null)
    {
        $data['title_en'] = $componentsData->title_en;
        $data['title_bn'] = $componentsData->title_bn;
        $data['component_type'] = $componentsData->component_type;
        if ($type == 'press_release' || $type == 'news_events'){
            foreach ($componentsData->items as $id){
                $data['sliding_speed'] = $componentsData->sliding_speed;

                $pressNewsEvent = $this->mediaPressNewsEventRepository->getPressNewsEvent($type, $id);

                if ($pressNewsEvent) {
                   $pressNewsEvent = $this->getPressNewsImgData($pressNewsEvent);
                    $data['data'][] = $pressNewsEvent;
                }
            }
        } else {
            foreach ($componentsData->items as $id){
                $video = $this->mediaTvcVideoRepository->getVideoItems($id);
                if ($video){
                    $data['data'][] = $video;
                }
            }
        }
        return $data;
    }

    public function getPressNewsImgData($pressNewsEvent)
    {
        $thumbKeyData = config('filesystems.moduleType.MediaPressNewsEventThumbnail');
        $detailsKeyData = config('filesystems.moduleType.MediaPressNewsEventDetails');

        $thumbnailData = $this->imageFileViewerService->prepareImageData($pressNewsEvent, $thumbKeyData);
        $detailsImgData = $this->imageFileViewerService->prepareImageData($pressNewsEvent, $detailsKeyData);

        $pressNewsEvent->thumbnail_image_en = $thumbnailData['image_url_en'] ?? '/uploads/' . $pressNewsEvent->thumbnail_image;
        $pressNewsEvent->thumbnail_image_bn = $thumbnailData['image_url_en'] ?? '/uploads/' . $pressNewsEvent->thumbnail_image;
        $pressNewsEvent->details_image_en = $detailsImgData['image_url_en'] ?? '/uploads/' . $pressNewsEvent->details_image;
        $pressNewsEvent->details_image_bn = $detailsImgData['image_url_en'] ?? '/uploads/' . $pressNewsEvent->details_image;

        unset($pressNewsEvent->details_image, $pressNewsEvent->thumbnail_image, $pressNewsEvent->details_image_name_en,
            $pressNewsEvent->details_image_name_bn, $pressNewsEvent->thumbnail_image_name_en, $pressNewsEvent->thumbnail_image_name_bn);

        return $pressNewsEvent;
    }

    public function factoryComponent($componentsData) {
        $data = null;
        switch ($componentsData->component_type) {
            case "news_carousel_slider":
                $data = $this->getMediaFeatureData($componentsData,'news_events');
                break;
            case "press_slider":
                $data = $this->getMediaFeatureData($componentsData,'press_release');
                break;
            case "video":
                $data = $this->getMediaFeatureData($componentsData, 'video');
                break;
        }
        return $data;
    }

    public function landingData()
    {
        $orderBy = ['column' => "display_order", 'direction' => 'ASC'];
        $components = $this->mediaLandingPageRepository->findAll('', '', $orderBy);
        foreach ($components as $items){
            $allComponents[] = $this->factoryComponent($items);
        }

        $bannerImage = $this->mediaBannerImageRepository->bannerImage('landing_page');
        $bannerKey = config('filesystems.moduleType.MediaBannerImage');
        $imgData = $this->imageFileViewerService->prepareImageData($bannerImage, $bannerKey);
        $bannerImage = array_merge($bannerImage->toArray(), $imgData);
        unset($bannerImage['banner_image_url'], $bannerImage['banner_mobile_view']);

        $data = [
            'components' => isset($allComponents) ? $allComponents : [],
            'banner_image' => (object) $bannerImage
        ];
        return $this->sendSuccessResponse($data, 'Media Landing Page data');
    }

}
