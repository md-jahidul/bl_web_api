<?php

/**
 * Created by PhpStorm.
 * User: BS23
 * Date: 26-Aug-19
 * Time: 4:34 PM
 */

namespace App\Repositories;

use App\Models\OfferCategory;

class OfferCategoryRepository extends BaseRepository
{
    public $modelName = OfferCategory::class;

    public function categories()
    {
        return $this->model->where('parent_id', 0)
            ->with('children')
            ->select('id', 'parent_id', 'name_en', 'name_bn', 'alias', 'banner_alt_text', 'banner_alt_text_bn', 'url_slug', 'url_slug_bn',
                'banner_name', 'banner_name_bn', 'schema_markup', 'page_header', 'page_header_bn', 'banner_image_url', 'banner_image_mobile',
                'type_id')
            ->get();
    }
}
