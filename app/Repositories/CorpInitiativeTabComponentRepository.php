<?php

/**
 * Created by PhpStorm.
 * User: bs-205
 * Date: 18/08/19
 * Time: 17:07
 */

namespace App\Repositories;

use App\Models\CorpInitiativeTabComponent;

class CorpInitiativeTabComponentRepository extends BaseRepository
{
    public $modelName = CorpInitiativeTabComponent::class;


    public function list($tabId)
    {
        return $this->model->where('initiative_tab_id', $tabId)
            ->orderBy('component_order', 'ASC')
            ->get();
    }
}

