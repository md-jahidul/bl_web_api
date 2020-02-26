<?php

/**
 * Created by PhpStorm.
 * User: BS23
 * Date: 27-Aug-19
 * Time: 3:51 PM
 */

namespace App\Repositories;

use App\Models\AppServiceProduct;

class AppServiceProductRepository extends BaseRepository
{
    public $modelName = AppServiceProduct::class;


    public function appServiceTab()
    {
        return $this->belongsTo(AppServiceTab::class, 'id', 'app_service_tab_id');
    }


    public function appServiceCategory()
    {
        return $this->hasMany(AppServiceCategory::class, 'id', 'app_service_cat_id');
    }
}
