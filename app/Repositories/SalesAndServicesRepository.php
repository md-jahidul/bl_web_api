<?php

namespace App\Repositories;

use App\Models\StoreLocator;

class SalesAndServicesRepository extends BaseRepository
{
    public $modelName = StoreLocator::class;

    public function getServiceCenterByDistrict($district = null)
    {
    	if( !empty($district) ){
    		return $this->model->where('district', $district)->orderBy('district', 'ASC')->get();
    	}
    	else{
    		return $this->model->orderBy('district', 'ASC')->get();
    	}
        
    }
}
