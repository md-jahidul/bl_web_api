<?php

namespace App\Repositories;

use App\Models\CorpInitiativeTab;

class CorporateInitiativeTabRepository extends BaseRepository
{
    public $modelName = CorpInitiativeTab::class;

    public function getTabs()
    {
        return $this->model->where('status', 1)
            ->select('title_en', 'title_bn', 'url_slug_en', 'page_header', 'page_header_bn', 'schema_markup')
            ->orderBy('display_order', 'ASC')
            ->get();
    }
}
