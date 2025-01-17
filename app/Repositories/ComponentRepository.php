<?php

/**
 * Created by PhpStorm.
 * User: bs-205
 * Date: 18/08/19
 * Time: 17:07
 */

namespace App\Repositories;

use App\Models\AlSlider;
use App\Models\AppServiceCategory;
use App\Models\AppServiceProduct;
use App\Models\AppServiceProductDetail;
use App\Models\Component;

class ComponentRepository extends BaseRepository
{
    public $modelName = Component::class;

    public function getComponentByPageType($pageType)
    {
        return $this->model
             ->orderBy('component_order', 'ASC')
             ->where('page_type', $pageType)
             ->select(
                 'id', 'section_details_id', 'page_type',
                 'component_type', 'title_en', 'title_bn',
                 'editor_en', 'editor_bn', 'extra_title_bn',
                 'extra_title_en', 'multiple_attributes',
                 'video', 'image', 'alt_text', 'other_attributes'
             )
             ->where('status', 1)
            ->get();
    }

}
