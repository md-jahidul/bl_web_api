<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\EasyPaymentCard;
use Illuminate\Http\Request;

class EasyPaymentCardController extends Controller
{
     private $easyPaymentCard;

    /**
     * EasyPaymentCardController constructor.
     * @param EasyPaymentCard $easyPaymentCard
     */
    public function __construct(EasyPaymentCard $easyPaymentCard) {
        $this->easyPaymentCard = $easyPaymentCard;
    }

    
    
    public function cardList(Request $request){
        $cards = $this->easyPaymentCard->select('code', 'division', 'area', 'branch_name','address');
        
        if($request->division){
            $cards->where('division', $request->division);
        }
        if($request->area){
            $cards->where('area', $request->area);
        }
        
        $cardList = $cards->get();
        
        $divisions = $this->easyPaymentCard->select('division')->groupBy('division')->get();
        $divs = [];
        foreach($divisions as $v){
           $divs[] =  $v['division'];
        }
        
        return array('divisions' => $divs, 'cardList' => $cardList);
    }
    
    public function getAreaList(Request $request){
        $division = $request->division;
       $areaList = $this->easyPaymentCard->select('area')->where('division', "$division")->groupBy('area')->get(); 
        $areas = [];
        foreach($areaList as $v){
           $areas[] =  $v['area'];
        }
       return $areas;
    }


}
