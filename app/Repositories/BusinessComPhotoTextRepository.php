<?php

/**
 * User: Bulbul Mahmud Nito
 * Date: 11/02/2020
 */

namespace App\Repositories;

use App\Models\BusinessPhotoText;

class BusinessComPhotoTextRepository extends BaseRepository {

    public $modelName = BusinessPhotoText::class;

    
    public function getComponent($serviceId){
        $component = $this->model->where('service_id', $serviceId)->get();
        return $component;
    }
    

}
