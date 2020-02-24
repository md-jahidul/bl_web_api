<?php

/**
 * User: Bulbul Mahmud Nito
 * Date: 19/02/2020
 */

namespace App\Repositories;

use App\Models\BusinessComPriceTable;

class BusinessComPriceTableRepository extends BaseRepository {

    public $modelName = BusinessComPriceTable::class;

    public function saveComponent($position, $title, $head, $colOne, $colTwo, $colThree, $srvsId, $oldComponents) {
        $data = [];

        $headJson = json_encode($head);

        $bodyOne = $colOne;
        $bodyTwo = $colTwo;
        $bodyThree = $colThree;
        $body = array(
            0 => $bodyOne, 1 => $bodyTwo, 2 => $bodyThree
        );
        $bodyJson = json_encode($body);

        $data[] = array(
            'title' => $title,
            'table_head' => $headJson,
            'table_body' => $bodyJson,
            'position' => $position + $oldComponents,
            'service_id' => $srvsId,
        );

        $this->model->insert($data);
    }
    
     public function getComponent($serviceId) {
        $component = $this->model->where('service_id', $serviceId)->get();
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
