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
     * FourGDevicesService constructor.
     * @param FourGDevicesRepository $fourGDevicesService
     * @param FourGLandingPageRepository $fourGLandingPageRepository
     */
    public function __construct(
        FourGDevicesRepository $fourGDevicesService,
        FourGLandingPageRepository $fourGLandingPageRepository
    ) {
        $this->fourGDevicesService = $fourGDevicesService;
        $this->fourGLandingPageRepository = $fourGLandingPageRepository;
        $this->setActionRepository($fourGDevicesService);
    }


    public function fourGDevice()
    {
        $fourGComponent = $this->fourGLandingPageRepository->getComponent('four_g_devices');
        $deviceOffers = $this->fourGDevicesService->devices();
        $collection = [
            'component_title_en' => $fourGComponent->title_en,
            'component_title_bn' => $fourGComponent->title_bn,
            'component_description_en' => $fourGComponent->description_en,
            'component_description_bn' => $fourGComponent->description_bn,
            'current_page' => $deviceOffers->currentPage(),
            'devices' => $deviceOffers->items(),
            'last_page' => $deviceOffers->lastPage(),
            'per_page' => $deviceOffers->perPage(),
            'total' => $deviceOffers->total()
        ];
        $data = json_decode(json_encode($collection), true);

        return $this->sendSuccessResponse($data, '4G Devices');
    }
}
