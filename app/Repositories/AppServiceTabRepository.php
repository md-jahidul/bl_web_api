<?php

/**
 * Created by PhpStorm.
 * User: BS23
 * Date: 27-Aug-19
 * Time: 3:51 PM
 */

namespace App\Repositories;

use App\Models\AppServiceTab;

class AppServiceTabRepository extends BaseRepository
{
    public $modelName = AppServiceTab::class;

    public function appServiceCollection()
    {
        return $this->model
            ->where('status', 1)
            ->with(['categories' => function ($category) {
                    $category->where('status', 1);
                    $category->with(['products' => function ($product) {
                        $product->select([
                            'id', 'app_service_tab_id',
                            'app_service_cat_id',
                            'tag_category_id',
                            'name_en', 'name_bn',
                            'description_en',
                            'description_bn',
                            'price_tk',
                            'validity_unit',
                            'product_img_url',
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
                            'other_info'
                        ])
                        ->where('status', 1)
                        ->checkStartEndDate();
                    }]);
                    $category->select('id', 'app_service_tab_id', 'title_en', 'title_bn');
                }])
            ->select(
                'id',
                'name_en',
                'name_bn',
                'banner_image_url',
                'banner_image_mobile',
                'banner_alt_text',
                'url_slug',
                'schema_markup',
                'page_header',
                'alias'
            )
            ->get();
    }
}
