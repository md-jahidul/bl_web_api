<?php

/**
 * Created by PhpStorm.
 * User: BS23
 * Date: 27-Aug-19
 * Time: 3:51 PM
 */

namespace App\Repositories;

use App\Models\AdTech;

class AdTechRepository extends BaseRepository
{
    public $modelName = AdTech::class;

    public function getSearchAdTech($type)
    {
        return $this->model->where('reference_type', $type)
            ->where('status', 1)
            ->select('img_url', 'img_name_en', 'img_name_bn', 'alt_text_en', 'alt_text_bn', 'redirect_url_en', 'redirect_url_bn', 'is_external_url', 'external_url')
            ->first();
    }
}
