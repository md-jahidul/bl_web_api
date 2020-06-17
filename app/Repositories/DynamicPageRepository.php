<?php

namespace App\Repositories;

use App\Models\OtherDynamicPage;

class DynamicPageRepository extends BaseRepository {

    public $modelName = OtherDynamicPage::class;

    public function page($slug)
    {
        return $this->model->where('url_slug', $slug)
            ->select(
                'id', 'page_header', 'schema_markup',
                'banner_image_url', 'banner_mobile_view',
                'alt_text', 'page_name_en', 'page_name_bn',
                'url_slug'
            )
            ->with(['components' => function($q){
                $q->orderBy('component_order', 'ASC')
                    ->where('page_type', 'other_dynamic_page')
                    ->select(
                        'id', 'section_details_id', 'page_type',
                        'component_type', 'title_en', 'title_bn',
                        'editor_en', 'editor_bn', 'extra_title_bn',
                        'extra_title_en', 'multiple_attributes',
                        'other_attributes'
                    )
                    ->where('status', 1);
            }])
            ->first();
    }
}