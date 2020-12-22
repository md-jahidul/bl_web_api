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
            ->select('id',
                'name_en',
                'name_bn',
                'banner_image_url' ,
                'banner_image_mobile',
                'banner_alt_text' ,
                'banner_alt_text_bn',
                'banner_name',
                'banner_name_bn',
                'url_slug' ,
                'url_slug_bn' ,
                'schema_markup' ,
                'page_header',
                'page_header_bn' ,
                'alias')
            ->get();
    }
}
