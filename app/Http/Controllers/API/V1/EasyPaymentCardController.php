<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Services\Banglalink\EasyPaymentCardService;
use Illuminate\Http\Request;

class EasyPaymentCardController extends Controller
{
     private $easyPaymentCardService;

    /**
     * EasyPaymentCardController constructor.
     * @param EasyPaymentCardService $easyPaymentCardService
     */
    public function __construct(EasyPaymentCardService $easyPaymentCardService) {
        $this->easyPaymentCardService = $easyPaymentCardService;
    }

    
    
    public function cardList($division = "", $area = ""){
        
         return $this->easyPaymentCardService->getCardList($division, $area);
    }
    
    public function getAreaList($division){
       return $this->easyPaymentCardService->getAreaList($division);
    }


}
