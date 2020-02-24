<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Services\Banglalink\DeviceOfferService;
use Illuminate\Http\Request;

class DeviceOfferController extends Controller
{
     private $deviceOfferService;

    /**
     * EasyPaymentCardController constructor.
     * @param DeviceOfferService $deviceOfferService
     */
    public function __construct(DeviceOfferService $deviceOfferService) {
        $this->deviceOfferService = $deviceOfferService;
    }

    
    
    public function offerList($brand = ""){
        
         return $this->deviceOfferService->getOfferList($brand);
    }
    

}
