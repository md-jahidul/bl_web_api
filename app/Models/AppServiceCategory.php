<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppServiceCategory extends Model
{
    protected $fillable = ['app_service_tab_id', 'title_en', 'title_bn', 'alias', 'other_attributes', 'status'];

    public function appServiceTab()
    {
        return $this->belongsTo(AppServiceTab::class, 'app_service_tab_id', 'id');
    }

    public function products()
    {
        return $this->hasMany(AppServiceProduct::class, 'app_service_cat_id', 'id');
    }
}
