<?php

/**
 * Created by PhpStorm.
 * User: BS23
 * Date: 27-Aug-19
 * Time: 1:15 PM
 */

namespace App\Services;

use App\Repositories\FourGDevicesRepository;
use App\Repositories\FourGLandingPageRepository;
use App\Traits\CrudTrait;

class FourGDevicesService extends ApiBaseService
{
    use CrudTrait;
    /**
     * @var FourGDevicesService
     */
    private $fourGDevicesService;
    /**
     * @var FourGLandingPageRepository
     */
    private $fourGLandingPageRepository;

    /**
     * @var ImageFileViewerService
     */
    private $imageFileViewerService;

    /**
     * FourGDevicesService constructor.
     * @param FourGDevicesRepository $fourGDevicesService
     * @param FourGLandingPageRepository $fourGLandingPageRepository
     * @param ImageFileViewerService $imageFileViewerService
     */
    public function __construct(
        FourGDevicesRepository $fourGDevicesService,
        FourGLandingPageRepository $fourGLandingPageRepository,
        ImageFileViewerService $imageFileViewerService
    ) {
        $this->fourGDevicesService = $fourGDevicesService;
        $this->fourGLandingPageRepository = $fourGLandingPageRepository;
        $this->imageFileViewerService = $imageFileViewerService;
        $this->setActionRepository($fourGDevicesService);
    }


    public function fourGDevice()
    {
        $fourGComponent = $this->fourGLandingPageRepository->getComponent('four_g_devices');
        $deviceOffers = $this->fourGDevicesService->devices();
        $devices = $this->getDeviceRelatedImgData($deviceOffers);

        $collection = [
            'component_title_en' => $fourGComponent->title_en,
            'component_title_bn' => $fourGComponent->title_bn,
            'devices' => $devices,
            'current_page' => $deviceOffers->currentPage(),
            'last_page' => $deviceOffers->lastPage(),
            'per_page' => $deviceOffers->perPage(),
            'total' => $deviceOffers->total()
        ];
        $data = json_decode(json_encode($collection), true);

        return $this->sendSuccessResponse($data, '4G Devices');
    }

    public function getDeviceRelatedImgData($deviceOffers)
    {
        $devices = [];
        $logoKeyData = config('filesystems.moduleType.FourGDeviceLogo');
        $thumbKeyData = config('filesystems.moduleType.FourGDeviceLogo');

        foreach ($deviceOffers->items() as $key => $item) {
            $logoImgData = $this->imageFileViewerService->prepareImageData($item, $logoKeyData);
            $thumbImgData = $this->imageFileViewerService->prepareImageData($item, $thumbKeyData);

            $item->logo_img_name_en = $logoImgData['image_url_en'] ?? '';
            $item->logo_img_name_bn = $logoImgData['image_url_bn'] ?? '';
            $item->thumbnail_img_name_en = $thumbImgData['image_url_en'] ?? '';
            $item->thumbnail_img_name_bn = $thumbImgData['image_url_bn'] ?? '';
            unset($item->card_logo, $item->thumbnail_image);

            $devices[$key] = $item;
        }

        return $devices;
    }
}
