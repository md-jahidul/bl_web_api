<?php

/**
 * User: Md Fahreyad Hossain
 * Date: 1/1/2023
 */

namespace App\Repositories;

use App\Http\Resources\SubFooterResource;
use App\Models\AlSliderImage;
use App\Models\SubFooter;

class SubFooterRepository extends BaseRepository {

    public $modelName = SubFooter::class;

    public function getData(){
        return new SubFooterResource($this->findOne(1));
    }

}
