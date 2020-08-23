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
     * DigitalServicesService constructor.
     * @param BeAPartnerRepository $beAPartnerRepository
     */
    public function __construct(
        BeAPartnerRepository $beAPartnerRepository
    ) {
        $this->beAPartnerRepository = $beAPartnerRepository;
        $this->setActionRepository($beAPartnerRepository);
    }

    public function beAPartnerData()
    {
        $data = $this->beAPartnerRepository->getOneData();
        return $this->sendSuccessResponse($data, 'Be a partner Data');
    }

}
