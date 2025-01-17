<?php

namespace App\Repositories;

use App\Models\AboutUsBanglalink;
use App\Models\CorpCrStrategyComponent;
use App\Models\CorporateCrStrategySection;

class CorpCrStrategyComponentRepository extends BaseRepository
{
    public $modelName = CorpCrStrategyComponent::class;

    protected const PAGE_TYPE = "cr_strategy_component_details";

    public function componentWithDetails($urlSlug)
    {
        return $this->model->where('url_slug_en', $urlSlug)
            ->orWhere('url_slug_bn', $urlSlug)
            ->where('status', 1)
            ->select(
                'id', 'title_en', 'title_bn', 'details_en', 'details_bn',
                'url_slug_en', 'url_slug_bn', 'page_header', 'page_header_bn', 'schema_markup', 'banner'
            )
            ->with(['components' => function ($q) {
                $q->orderBy('component_order', 'ASC')
                    ->where('page_type', self::PAGE_TYPE)
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
