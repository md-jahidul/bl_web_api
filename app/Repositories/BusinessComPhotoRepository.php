<?php

/**
 * User: Bulbul Mahmud Nito
 * Date: 11/02/2020
 */

namespace App\Repositories;

use App\Models\BusinessComPhoto;

class BusinessComPhotoRepository extends BaseRepository {

    public $modelName = BusinessComPhoto::class;

    public function saveComponent($position, $onePath, $twoPath, $threePath, $fourPath, $serviceId, $oldComponents) {
        $this->model->insert(
                array(
                    "photo_one" => $onePath,
                    "photo_two" => $twoPath,
                    "photo_three" => $threePath,
                    "photo_four" => $fourPath,
                    "position" => $position + $oldComponents,
                    "service_id" => $serviceId
                )
        );
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
