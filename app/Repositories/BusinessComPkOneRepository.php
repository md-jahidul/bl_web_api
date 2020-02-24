<?php

/**
 * User: Bulbul Mahmud Nito
 * Date: 11/02/2020
 */

namespace App\Repositories;

use App\Models\BusinessComPackageOne;
use DB;

class BusinessComPkOneRepository extends BaseRepository {

    public $modelName = BusinessComPackageOne::class;

    public function saveComponent($position, $head, $ftText, $price, $srvsId, $oldComponents) {
        $data = [];
        foreach($head as $k => $v){
           $data[] = array(
               'table_head' => $v,
               'feature_text' => $ftText[$k],
               'price' => $price[$k],
               'position' => $position+$oldComponents,
               'service_id' => $srvsId,
           ); 
        }
        
        $this->model->insert($data);
    }
    
       public function getComponent($serviceId) {
        $component = $this->model->select(DB::raw('group_concat(table_head) heads, position'))
                        ->where('service_id', $serviceId)->groupBy('position')->get();
        return $component;
    }
    
     public function deleteComponent($serviceId, $position){
        $component = $this->model->where(array('service_id' => $serviceId, 'position' => $position))->delete();
        return $component;
    }
    
    public function changePosition($serviceId, $newPosition, $oldPosition){
        $component = $this->model->where(array('service_id' => $serviceId, 'position' => $oldPosition))
                ->update(array('position' => $newPosition));
        return $component;
    }

}
