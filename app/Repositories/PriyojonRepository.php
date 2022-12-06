<?php

namespace App\Repositories;

use App\Models\AboutUsBanglalink;
use App\Models\Priyojon;

/**
 * Class AboutUsRepository
 * @package App\Repositories
 */
class PriyojonRepository extends BaseRepository
{
    protected $modelName = Priyojon::class;

    public function getMenuForSlug($alias)
    {
        return $this->model->where('alias', $alias)->first();
    }

    public function landingPageBannerAndContent()
    {
        return $this->model
            ->where('status', 1)
            ->where('parent_id', 0)
            ->whereNotNull('component_type')
            ->select('id', 'title_en', 'title_bn', 'desc_en', 'desc_bn', 'banner_image_url', 'banner_mobile_view', 'alt_text_en')
            ->with(['children' => function($q){
                $q->where('status', 1)
                    ->select('parent_id', 'title_en', 'title_bn', 'url', 'url_slug_en', 'url_slug_bn', 'page_header', 'page_header_bn', 'schema_markup', 'alias');
            }])
            ->get();
    }
}
