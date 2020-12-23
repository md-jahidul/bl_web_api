<?php

/**
 * Created by PhpStorm.
 * User: BS23
 * Date: 27-Aug-19
 * Time: 3:51 PM
 */

namespace App\Repositories;

use App\Models\AppServiceCategory;

class AppServiceCategoryRepository extends BaseRepository
{
    public $modelName = AppServiceCategory::class;

    public function getCategoriesByTab($tabId)
    {
        return $this->model->where('app_service_tab_id', $tabId)
                            ->where('status', 1)
                            ->select('id', 'app_service_tab_id', 'title_en', 'title_bn')
                            ->get();
    }

}
