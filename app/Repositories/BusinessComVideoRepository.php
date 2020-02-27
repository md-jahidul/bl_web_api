<?php

/**
 * User: Bulbul Mahmud Nito
 * Date: 11/02/2020
 */

namespace App\Repositories;

use App\Models\BusinessComVideo;

class BusinessComVideoRepository extends BaseRepository {

    public $modelName = BusinessComVideo::class;

    public function saveComponent($position, $name, $title, $embed, $description, $serviceId, $oldComponents) {
        $this->model->insert(
                array(
                    "name" => $name,
                    "title" => $title,
                    "embed_code" => $embed,
                    "description" => $description,
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
