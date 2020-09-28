<?php

namespace App\Repositories;

use App\Models\AboutUsBanglalink;
use App\Models\CorpCrStrategyComponent;
use App\Models\CorporateCrStrategySection;

class CorpCrStrategyComponentRepository extends BaseRepository
{
    public $modelName = CorpCrStrategyComponent::class;

    public function getComponentBySecton($pageType, $sectionId)
    {
        return $this->model->where('page_type', $pageType)
            ->where('page_id', $sectionId)
            ->get();
    }
}
