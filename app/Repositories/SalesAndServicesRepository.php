<?php

namespace App\Repositories;

use App\Models\StoreLocator;

class SalesAndServicesRepository extends BaseRepository
{  
	/**
	 * [$modelName description]
	 * @var [type]
	 */
	public $modelName = StoreLocator::class;

	 /**
	  * [getServiceCenterByDistrict description]
	  * @param  [type] $district [description]
	  * @return [type]           [description]
	  */
	 public function getServiceCenterByDistrict($district = null)
	 {
		if( !empty($district) ){
			return $this->model->where('district', $district)->orderBy('district', 'ASC')->get();
		}
		else{
			return $this->model->orderBy('district', 'ASC')->get();
		}
		  
	}


	public function getServiceCenterByDistrictThana($district, $thana)
	{
		return $this->model->where('district', $district)->where('thana', $thana)->orderBy('district', 'ASC')->orderBy('thana', 'ASC')->get();
	}
}
