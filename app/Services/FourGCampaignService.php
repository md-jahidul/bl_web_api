<?php

/**
 * Created by PhpStorm.
 * User: BS23
 * Date: 27-Aug-19
 * Time: 1:15 PM
 */

namespace App\Services;

use App\Repositories\FourGCampaignRepository;
use App\Repositories\FourGDevicesRepository;
use App\Repositories\FourGLandingPageRepository;
use App\Traits\CrudTrait;

class FourGCampaignService extends ApiBaseService
{
    use CrudTrait;
    /**
     * @var FourGLandingPageRepository
     */
    private $fourGLandingPageRepository;
    /**
     * @var FourGCampaignRepository
     */
    private $fourGCampaignRepository;
    /**
     * @var ImageFileViewerService
     */
    private $imageFileViewerService;

    /**
     * FourGDevicesService constructor.
     * @param FourGCampaignRepository $fourGCampaignRepository
     * @param FourGLandingPageRepository $fourGLandingPageRepository
     * @param ImageFileViewerService $imageFileViewerService
     */
    public function __construct(
        FourGCampaignRepository $fourGCampaignRepository,
        FourGLandingPageRepository $fourGLandingPageRepository,
        ImageFileViewerService $imageFileViewerService
    ) {
        $this->fourGCampaignRepository = $fourGCampaignRepository;
        $this->imageFileViewerService = $imageFileViewerService;
        $this->fourGLandingPageRepository = $fourGLandingPageRepository;
    }


    public function getCampWithBanner()
    {
        $fourGComponent = $this->fourGLandingPageRepository->getComponent('four_g_campaign');
        $fourGBanner = $this->fourGLandingPageRepository->getBannerImage();

        $bannerKey = config('filesystems.moduleType.FourGLandingPage');

        $fourGBanner = (object) array_merge($fourGBanner->items, $this->imageFileViewerService->prepareImageData($fourGBanner->items, $bannerKey));

        unset($fourGBanner->banner_image_url, $fourGBanner->banner_mobile_view);

        $campaign = $this->fourGCampaign();

        $collection = [
            'component_title_en' => $fourGComponent->title_en,
            'component_title_bn' => $fourGComponent->title_bn,
            'campaign' => $campaign,
            'banner' => $fourGBanner
        ];
        $data = json_decode(json_encode($collection), true);

        return $this->sendSuccessResponse($data, '4G Campaign With Banner Image');
    }

    public function fourGCampaign()
    {
        $campaign = $this->fourGCampaignRepository->findOneByProperties(['status' => 1], [
            'details_en', 'details_bn', 'image_url', 'alt_text_en', 'alt_text_bn'
        ]);

        $campaignKeyData = config('filesystems.moduleType.FourGCampaign');

        $campaign = (object) array_merge($campaign->toArray(), $this->imageFileViewerService->prepareImageData($campaign, $campaignKeyData));

        unset($campaign->image_url);

        return $campaign;
    }
}
