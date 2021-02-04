<?php

namespace App\Repositories;

use App\Models\OtherDynamicPage;

class DynamicPageRepository extends BaseRepository {

    public $modelName = OtherDynamicPage::class;

    public function page($slug)
    {
        return $this->model->where('url_slug', $slug)
            ->select(
                'id', 'page_header', 'schema_markup', 'banner_name',
                'banner_name_bn', 'banner_image_url', 'banner_mobile_view',
                'alt_text', 'page_name_en', 'page_name_bn',
                'page_content_en', 'page_content_bn',
                'url_slug', 'url_slug_bn'
            )
            ->with(['components' => function($q){
                $q->orderBy('component_order', 'ASC')
                    ->with('componentMultiData')
                    ->where('page_type', 'other_dynamic_page')
                    ->select(
                        'id', 'section_details_id', 'page_type',
                        'component_type', 'title_en', 'title_bn',
                        'editor_en', 'editor_bn', 'extra_title_bn',
                        'extra_title_en', 'multiple_attributes',
                        'video', 'image_name_en', 'image_name_bn',
                        'image', 'alt_text', 'alt_text_bn', 'other_attributes'
                    )
                    ->where('status', 1);
            }])
            ->first();
    }
}
