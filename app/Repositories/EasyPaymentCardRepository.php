<?php


namespace App\Repositories;


use App\Models\EasyPaymentCard;

class EasyPaymentCardRepository extends BaseRepository
{
    public $modelName = EasyPaymentCard::class;

    /**
     * @param $request
     * @return mixed
     */
    public function getList($division, $area)
    {
        $cards = $this->model->where('status', 1)
            ->select('branch_name', 'address');
        
        if(!empty($division)){
            $cards->where('division', $division);
        }
        if(!empty($area)){
            $cards->where('area', $area);
        }
        
        $cardList = $cards->get();
        
        $divisions = $this->model->select('division')->groupBy('division')->get();
        $divs = [];
        foreach($divisions as $v){
           $divs[] =  $v['division'];
        }
        
        return array('divisions' => $divs, 'cardList' => $cardList);
    }
    
    public function getAreas($division)
    {
       $areaList = $this->model->select('area')->where('division', "$division")->groupBy('area')->get(); 
        $areas = [];
        foreach($areaList as $v){
           $areas[] =  $v['area'];
        }
       return $areas;
    }
}
