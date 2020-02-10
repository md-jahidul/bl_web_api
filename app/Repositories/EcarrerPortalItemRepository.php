<?php

/**
 * Created by PhpStorm.
 * User: BS23
 * Date: 27-Aug-19
 * Time: 3:51 PM
 */

namespace App\Repositories;

use App\Models\EcarrerPortalItem;
use Carbon\Carbon;

class EcarrerPortalItemRepository extends BaseRepository
{
    public $modelName = EcarrerPortalItem::class;


    /**
     * [getItemsByParentID description]
     * @param  [type] $parent_id [description]
     * @return [type]            [description]
     */
    public function getItemsByParentID($parent_id){

    	return $this->model::where('ecarrer_portals_id', '=', $parent_id)->whereNull('deleted_at')->get();

    }

    /**
     * [getSingleItemByID description]
     * @param  [type] $parent_id [description]
     * @param  [type] $id        [description]
     * @return [type]            [description]
     */
    public function getSingleItemByID($parent_id, $id){

    	return $this->model::where('id', $id)->where('ecarrer_portals_id', '=', $parent_id)->whereNull('deleted_at')->first();

    }

    /**
     * [sectionItemSoftDeleteBySectionID description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function sectionItemSoftDeleteBySectionID($id){

        return $this->model::where('ecarrer_portals_id', $id)->update(['deleted_at' => Carbon::now()]);

    }

}
