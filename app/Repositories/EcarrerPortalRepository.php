<?php

/**
 * Created by PhpStorm.
 * User: BS23
 * Date: 27-Aug-19
 * Time: 3:51 PM
 */

namespace App\Repositories;

use App\Models\EcarrerPortal;

class EcarrerPortalRepository extends BaseRepository
{
    public $modelName = EcarrerPortal::class;


    /**
     * [getSectionsByCategory description]
     * @param  [type] $category [description]
     * @return [type]           [description]
     */
    public function getSectionsByCategory($category){
    		return $this->model::with('portalItems')->where('category', '=', $category)->where('is_active', 1)->whereNull('deleted_at')->get();
    }

    /**
     * [getSectionSlugByID description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function getSectionSlugByID($id){
        return $this->model::where('id', $id)->whereNull('deleted_at')->first()->category;
    }


    public function getParentRouteSlugByID($id){
    	return $this->model::where('id', $id)->whereNull('deleted_at')->first()->route_slug;
    }

    public function getSectionDataByID($id){
        return $this->model::where('id', $id)->whereNull('deleted_at')->first();
    }

    /**
     * Retrieve eCareerInfo
     *
     * @return mixed
     */
    public function getEcareersInfo()
    {
        return $this->model->with('portalItems')->get();
    }
}