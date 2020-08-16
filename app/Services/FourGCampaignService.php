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
     * FourGDevicesService constructor.
     * @param FourGCampaignRepository $fourGCampaignRepository
     * @param FourGLandingPageRepository $fourGLandingPageRepository
     */
    public function __construct(
        FourGCampaignRepository $fourGCampaignRepository,
        FourGLandingPageRepository $fourGLandingPageRepository
    ) {
        $this->fourGCampaignRepository = $fourGCampaignRepository;
        $this->fourGLandingPageRepository = $fourGLandingPageRepository;
    }


    public function getCampWithBanner()
    {
        $fourGComponent = $this->fourGLandingPageRepository->getComponent('four_g_campaign');
        $fourGBanner = $this->fourGLandingPageRepository->getBannerImage();
        $campaign = $this->fourGCampaignRepository->findOneByProperties(['status' => 1], [
            'details_en', 'details_bn', 'image_url', 'alt_text_en', 'alt_text_bn'
        ]);
        $collection = [
            'component_title_en' => $fourGComponent->title_en,
            'component_title_bn' => $fourGComponent->title_bn,
            'campaign' => $campaign,
            'banner' => $fourGBanner->items
        ];
        $data = json_decode(json_encode($collection), true);

        return $this->sendSuccessResponse($data, '4G Campaign With Banner Image');
    }
}
