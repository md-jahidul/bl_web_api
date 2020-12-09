<?php

/**
 * Created by PhpStorm.
 * User: bs-205
 * Date: 18/08/19
 * Time: 17:07
 */

namespace App\Repositories;

use App\Models\CorpResContactUsPage;

class CorpRespContactUsRepository extends BaseRepository
{
    public $modelName = CorpResContactUsPage::class;

    public function getContactContent($pageType)
    {
        return $this->model->where('page_type', $pageType)
            ->where('status', 1)
            ->with(['fields' => function($q){
                $q->select('id', 'page_id', 'input_label_en', 'input_label_bn', 'field_name', 'data_type', 'type');
            }])
            ->select('id', 'page_type', 'component_title_en', 'component_title_bn', 'address_en', 'address_bn', 'send_button_en', 'send_button_bn')
            ->first();
    }
}
