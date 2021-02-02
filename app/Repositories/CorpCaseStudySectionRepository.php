<?php

namespace App\Repositories;

use App\Models\CorpCaseStudyReportSection;

class CorpCaseStudySectionRepository extends BaseRepository
{
    public $modelName = CorpCaseStudyReportSection::class;

    public function getSections()
    {
        return $this->model->select('id', 'section_type')
            ->with(['components' => function($q){
                $q->select('id',
                    'section_id', 'title_en',
                    'title_bn', 'details_en',
                    'details_bn', 'other_attributes',
                    'base_image', 'url_slug_en', 'url_slug_bn',
                    'image_name_en', 'image_name_bn',
                    'alt_text_en', 'alt_text_bn',
                    'page_header', 'page_header_bn', 'schema_markup');
            }])
            ->orderBy('display_order', 'ASC')
            ->get();
    }
}
