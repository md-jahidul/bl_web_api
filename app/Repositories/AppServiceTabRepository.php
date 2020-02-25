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
            ->with(['categories' => function ($query) {
                    $query->with(['products' => function ($product) {
                        $product->select(
                            'id', 'app_service_tab_id',
                            'app_service_cat_id',
                            'tag_category_id',
                            'name_en', 'name_bn',
                            'description_en',
                            'description_bn',
                            'price_tk',
                            'validity_unit',
                            'product_img_url',
                            'like', 'app_rating',
                            'can_active',
                            'ussd_en', 'ussd_bn',
                            'subscribe_text_en',
                            'subscribe_text_bn',
                            'send_to',
                            'app_store_link',
                            'google_play_link'
                        );
                    }]);
                    $query->select('id', 'app_service_tab_id', 'title_en', 'title_bn');
                }])
            ->select('id', 'name_en', 'name_bn', 'banner_image_url', 'banner_alt_text', 'alias')
            ->get();
    }
}
