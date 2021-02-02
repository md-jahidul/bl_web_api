<?php

namespace App\Repositories;

use App\Models\AboutUsBanglalink;
use App\Models\CorpCaseStudyReportComponent;
use App\Models\CorpCrStrategyComponent;
use App\Models\CorporateCrStrategySection;

class CorpCaseStudyComponentRepository extends BaseRepository
{
    public $modelName = CorpCaseStudyReportComponent::class;

    protected const PAGE_TYPE = "case_study_component_details";

    public function componentWithDetails($urlSlug)
    {
        return $this->model->where('url_slug_en', $urlSlug)
            ->where('status', 1)
            ->select(
                'id', 'title_en', 'title_bn', 'details_en', 'details_bn',
                'url_slug_en', 'url_slug_bn', 'banner'
            )
            ->with(['components' => function ($q) {
                $q->orderBy('component_order', 'ASC')
                    ->where('page_type', self::PAGE_TYPE)
                    ->select(
                        'id', 'section_details_id', 'page_type',
                        'component_type', 'title_en', 'title_bn',
                        'editor_en', 'editor_bn', 'extra_title_bn',
                        'extra_title_en', 'multiple_attributes', 'image_name_en', 'image_name_bn',
                        'video', 'image', 'alt_text', 'alt_text_bn', 'other_attributes'
                    )
                    ->where('status', 1);
            }])
            ->first();
    }
}
