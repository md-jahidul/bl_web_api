<?php

/**
 * Created by PhpStorm.
 * User: BS23
 * Date: 27-Aug-19
 * Time: 3:51 PM
 */

namespace App\Repositories;

use App\Models\EcareerPortal;

class EcareerPortalRepository extends BaseRepository
{
    public $modelName = EcareerPortal::class;

    public function getSeoData($category){
        return $this->model->where('category_type', $category)->first();
    }

    /**
     * [getSectionsByCategory description]
     * @param  [type] $category [description]
     * @return [type]           [description]
     */
    public function getSectionsByCategory($category, $categoryTypes = null){

        if( empty($categoryTypes) ){
    		return $this->model::with(['portalItems' => function($query){

                $query->where('is_active', 1)->whereNull('deleted_at')->orderBy('display_order', 'asc');

            }])->where('category', '=', $category)->where('is_active', 1)->whereNull('deleted_at')->orderBy('display_order', 'asc')->get();
        }
        else{
            return $this->model::with(['portalItems' => function($query){
                $query->where('is_active', 1)->whereNull('deleted_at')->orderBy('display_order', 'desc');
            }])->where('category', '=', $category)->where('category_type', '=', $categoryTypes)->where('is_active', 1)->whereNull('deleted_at')->orderBy('display_order', 'asc')->get();
        }
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
        return $this->model->with(['portalItems' => function ($query) {
            $query->whereNull('deleted_at');
        }])->where('category', 'life_at_bl_diversity')->first();
    }

    public function findProgramId(){
        return $this->model::where('is_program',1)->first();

    }
}
