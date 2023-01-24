<?php

namespace App\Repositories;

use App\Models\FrontEndDynamicRoute;
use App\Models\OtherDynamicPage;

class DynamicPageRepository extends BaseRepository {

    public $modelName = OtherDynamicPage::class;

    public function page($slug)
    {

        return $this->model->where('url_slug', $slug)
            ->select(
                'id', 'page_header', 'page_header_bn', 'schema_markup',
                'banner_image_url', 'banner_mobile_view',
                'alt_text', 'page_name_en', 'page_name_bn',
                'page_content_en', 'page_content_bn',
                'url_slug', 'url_slug_bn'
            )
            ->with(['components' => function($q){
                $q->orderBy('component_order', 'ASC')
                    ->where('page_type', 'other_dynamic_page')
                    ->select(
                        'id', 'section_details_id', 'page_type',
                        'component_type', 'title_en', 'title_bn',
                        'editor_en', 'editor_bn', 'extra_title_bn',
                        'extra_title_en', 'multiple_attributes',
                        'video', 'image', 'alt_text', 'other_attributes'
                    )
                    ->where('status', 1);
            }])
            ->first();
    }
}
