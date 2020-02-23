<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppServiceProduct extends Model
{
    protected $guarded = ['id'];

    public function appServiceTab()
    {
        return $this->belongsTo(AppServiceTab::class, 'app_service_tab_id', 'id');
    }

    public function appServiceCat()
    {
        return $this->belongsTo(AppServiceCategory::class, 'app_service_cat_id', 'id');
    }
}
