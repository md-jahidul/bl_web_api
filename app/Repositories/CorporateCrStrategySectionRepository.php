<?php

namespace App\Repositories;

use App\Models\AboutUsBanglalink;
use App\Models\CorporateCrStrategySection;

class CorporateCrStrategySectionRepository extends BaseRepository
{
    public $modelName = CorporateCrStrategySection::class;

    public function getSections()
    {
        return $this->model->select('id', 'section_type', 'title_en', 'title_bn')
            ->with(['components' => function($q){
                $q->select('id',
                    'section_id', 'title_en',
                    'title_bn', 'details_en',
                    'details_en',
                    'other_attributes', 'url_slug_en',
                    'url_slug_bn',
                    'page_header', 'schema_markup');
                }])
            ->orderBy('display_order', 'ASC')
            ->get();
    }
}
