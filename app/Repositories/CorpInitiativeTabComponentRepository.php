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

    public function tabWiseComponent($tabId)
    {
        return $this->model->where('initiative_tab_id', $tabId)
            ->with(['multiComponent', 'batchTab' => function($q) {
                $q->with('batchTabComponents');
            }])
            ->select('id', 'initiative_tab_id', 'component_type', 'component_title_en', 'component_title_bn',
                'editor_en', 'editor_bn', 'multiple_attributes', 'single_base_image', 'single_alt_text_en',
                'single_alt_text_bn', 'single_image_name_en', 'single_image_name_bn')
            ->orderBy('component_order', 'ASC')
            ->get();
    }

    public function list($tabId)
    {
        return $this->model->where('initiative_tab_id', $tabId)
            ->orderBy('component_order', 'ASC')
            ->get();
    }
}

