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
                    'details_en',
                    'other_attributes', 'url_slug_en',
                    'page_header', 'schema_markup');
            }])
            ->orderBy('display_order', 'ASC')
            ->get();
    }
}
