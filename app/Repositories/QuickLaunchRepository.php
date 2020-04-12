<?php

namespace App\Repositories;

use App\Models\QuickLaunchItem;

class QuickLaunchRepository extends BaseRepository
{
    public $modelName = QuickLaunchItem::class;

    public function getQuickLaunch($type)
    {
        return $this->model->where('type', $type)->where('status', 1)->orderBy('display_order', 'ASC')->get();
    }
}
