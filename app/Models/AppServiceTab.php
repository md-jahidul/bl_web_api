<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppServiceTab extends Model
{
    protected $guarded = ['id'];

    public function categories()
    {
        return $this->hasMany(AppServiceCategory::class, 'app_service_tab_id', 'id');
    }

    public function products()
    {
        return $this->hasMany(AppServiceProduct::class, 'app_service_tab_id', 'id');
    }
}
