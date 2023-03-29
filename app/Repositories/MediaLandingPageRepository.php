<?php

/**
 * Created by PhpStorm.
 * User: bs-205
 * Date: 18/08/19
 * Time: 17:07
 */

namespace App\Repositories;

use App\Models\MediaLandingPage;

class MediaLandingPageRepository extends BaseRepository
{
    public $modelName = MediaLandingPage::class;

    public function getDataByRefType($referenceType)
    {
        return $this->model->where('reference_type', $referenceType)
            ->orderBy('display_order', 'ASC')
            ->get();
    }
}
