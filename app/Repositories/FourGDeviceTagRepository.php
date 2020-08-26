<?php

/**
 * Created by PhpStorm.
 * User: BS23
 * Date: 26-Aug-19
 * Time: 4:34 PM
 */

namespace App\Repositories;

use App\Models\FourGDeviceTag;

class FourGDeviceTagRepository extends BaseRepository
{
    public $modelName = FourGDeviceTag::class;

    public function getTags() {
        $response = $this->model->get();
        return $response;
    }
    public function getTagById($tagId) {
        $response = $this->model->findOrFail($tagId);
        return $response->name_en;
    }
}
