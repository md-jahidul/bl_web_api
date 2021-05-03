<?php

namespace App\Repositories;

use App\Models\CorpInitiativeTab;

class CorporateInitiativeTabRepository extends BaseRepository
{
    public $modelName = CorpInitiativeTab::class;

    public function getTabs()
    {
        return $this->model->where('status', 1)
            ->select('id', 'title_en', 'title_bn', 'url_slug_en', 'url_slug_bn', 'page_header', 'page_header_bn', 'schema_markup')
            ->orderBy('display_order', 'ASC')
            ->get();
    }

    public function getTabInfo($slug)
    {
        return $this->model->where('url_slug_en', $slug)
            ->orWhere('url_slug_bn', $slug)
            ->select('id', 'url_slug_en', 'url_slug_bn')
            ->first();
    }
}
