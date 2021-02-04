<?php

/**
 * Created by PhpStorm.
 * User: bs-205
 * Date: 18/08/19
 * Time: 17:23
 */

namespace App\Services;

use App\Repositories\AlFaqRepository;
use App\Repositories\BeAPartnerRepository;
use App\Repositories\ComponentRepository;
use App\Repositories\FourGCampaignRepository;
use App\Repositories\FourGLandingPageRepository;
use App\Repositories\MediaLandingPageRepository;
use App\Repositories\MediaPressNewsEventRepository;
use App\Repositories\MediaTvcVideoRepository;
use App\Traits\CrudTrait;
use App\Traits\FileTrait;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class BeAPartnerService extends ApiBaseService
{
    use CrudTrait;
    /**
     * @var BeAPartnerRepository
     */
    private $beAPartnerRepository;
    /**
     * @var ComponentRepository
     */
    private $componentRepository;

    protected const PAGE_TYPE = 'be_a_partner';
    /**
     * @var ImageFileViewerService
     */
    private $fileViewerService;

    /**
     * DigitalServicesService constructor.
     * @param BeAPartnerRepository $beAPartnerRepository
     * @param ComponentRepository $componentRepository
     * @param ImageFileViewerService $fileViewerService
     */
    public function __construct(
        BeAPartnerRepository $beAPartnerRepository,
        ComponentRepository $componentRepository,
        ImageFileViewerService $fileViewerService
    ) {
        $this->beAPartnerRepository = $beAPartnerRepository;
        $this->componentRepository = $componentRepository;
        $this->fileViewerService = $fileViewerService;
    }

    public function beAPartnerData()
    {
        $beAPartnerData = $this->beAPartnerRepository->getOneData();
        $components = $this->componentRepository->getComponentByPageType(self::PAGE_TYPE);

        $keyData = config('filesystems.moduleType.BeAPartner');
        $fileViewer = $this->fileViewerService->prepareImageData($beAPartnerData, $keyData);

        $data = [
          'title_en' => $beAPartnerData->title_en,
          'title_bn' => $beAPartnerData->title_bn,
          'description_en' => $beAPartnerData->description_en,
          'description_bn' => $beAPartnerData->description_bn,
          'vendor_button_en' => $beAPartnerData->vendor_button_en,
          'vendor_button_bn' => $beAPartnerData->vendor_button_bn,
          'vendor_portal_url' => $beAPartnerData->vendor_portal_url,
          'interested_button_en' => $beAPartnerData->interested_button_en,
          'interested_button_bn' => $beAPartnerData->interested_button_bn,
          'interested_url' => $beAPartnerData->interested_url,
          'banner_image_web_en' => isset($fileViewer["banner_image_web_en"]) ? $fileViewer["banner_image_web_en"] : null,
          'banner_image_web_bn' => isset($fileViewer['banner_image_web_bn']) ? $fileViewer['banner_image_web_bn'] : null,
          'banner_image_mobile_en' => isset($fileViewer["banner_image_mobile_en"]) ? $fileViewer["banner_image_mobile_en"] : null,
          'banner_image_mobile_bn' => isset($fileViewer['banner_image_mobile_bn']) ? $fileViewer['banner_image_mobile_bn'] : null,
          'components' => $components
        ];
        return $this->sendSuccessResponse($data, 'Be a partner Data');
    }

}
