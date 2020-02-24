<?php

/**
 * User: Bulbul Mahmud Nito
 * Date: 11/02/2020
 */

namespace App\Repositories;

use App\Models\BusinessPhotoText;

class BusinessComPhotoTextRepository extends BaseRepository {

    public $modelName = BusinessPhotoText::class;

    public function saveComponent($position, $text, $bannerPath, $serviceId, $oldComponents) {
        $this->model->insert(
                array(
                    "text" => $text,
                    "photo_url" => $bannerPath,
                    "position" => $position+$oldComponents,
                    "service_id" => $serviceId
                )
        );
    }
    
    public function getComponent($serviceId){
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
