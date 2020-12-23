<?php

/**
 * Created by PhpStorm.
 * User: BS23
 * Date: 27-Aug-19
 * Time: 3:51 PM
 */

namespace App\Repositories;

use App\Models\AppServiceCategory;
use App\Models\AppServiceProduct;
use App\Models\AppServiceTab;

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

    public function getProductInformationBySlug($slug)
    {
        return $this->model->where('url_slug', $slug)->orWhere('url_slug_bn', $slug)->first();
    }


    public function getProductsByCategory($catId)
    {
        return $this->model->where('app_service_cat_id', $catId)
                        ->where('status', 1)
                        ->select('id', 'app_service_tab_id',
                            'app_service_cat_id',
                            'tag_category_id',
                            'name_en', 'name_bn',
                            'description_en',
                            'description_bn',
                            'price_tk',
                            'validity_unit',
                            'product_img_url',
                            'product_img_en',
                            'product_img_bn',
                            'alt_text_en',
                            'alt_text_bn',
                            'like', 'app_review', 'app_rating',
                            'can_active',
                            'show_in_vas',
                            'show_subscribe',
                            'show_ussd',
                            'ussd_en', 'ussd_bn',
                            'subscribe_text_en',
                            'subscribe_text_bn',
                            'provider_url',
                            'send_to',
                            'app_store_link',
                            'google_play_link',
                            'url_slug',
                            'url_slug_bn',
                            'other_info')
                        ->checkStartEndDate()
                        ->get();
    }
}
