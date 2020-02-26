<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppServiceProductDetail extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'other_attributes' => 'array'
    ];

    // public function appServiceTab()
    // {
    //     return $this->belongsTo(AppServiceTab::class, 'app_service_tab_id', 'id');
    // }

    /**
     * App service details compoents
     * @return [type] [description]
     */
    public function detailsComponent()
    {
        return $this->hasMany(Component::class, 'section_details_id', 'id')->where('page_type', 'app_service');
    }
}
