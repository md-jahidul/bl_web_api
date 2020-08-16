<?php

namespace App\Http\Controllers\API\V1;

use App\Exceptions\IdpAuthException;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\FourGCampaignService;
use App\Services\FourGDevicesService;
use App\Services\FourGDeviceTagService;
use App\Services\ProductService;
use DB;
use App\Services\AboutUsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class BanglalinkFourGController extends Controller
{
    /**
     * @var ProductService
     */
    private $productService;
    /**
     * @var FourGDevicesService
     */
    private $fourGDevicesService;
    /**
     * @var FourGCampaignService
     */
    private $fourGCampaignService;

    /**
     * AboutUsController constructor.
     * @param ProductService $productService
     * @param FourGDevicesService $fourGDevicesService
     * @param FourGCampaignService $fourGCampaignService
     */
    public function __construct(
        ProductService $productService,
        FourGDevicesService $fourGDevicesService,
        FourGCampaignService $fourGCampaignService
    ) {
        $this->productService = $productService;
        $this->fourGDevicesService = $fourGDevicesService;
        $this->fourGCampaignService = $fourGCampaignService;
    }

    /**
     * @param $type
     * @return mixed
     */
    public function getFourGInternet($type)
    {
       return $this->productService->fourGInternet($type);
    }

    /**
     * @param $type
     * @return mixed
     */
    public function getFourGDevices()
    {
        return $this->fourGDevicesService->fourGDevice();
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getCampaignWithBanner()
    {
        return $this->fourGCampaignService->getCampWithBanner();
    }

//    /**
//     * @param Request $request
//     * @return JsonResponse
//     * @throws IdpAuthException
//     */
//    public function getEcareersInfo()
//    {
//        return $this->aboutUsService->getEcareersInfo();
//    }


}
