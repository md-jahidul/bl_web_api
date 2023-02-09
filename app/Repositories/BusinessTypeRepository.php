<?php

/**
 * User: Md Fahreyad Hossain
 * Date: 12/12/2022
 */

namespace App\Repositories;

use App\Models\BusinessType;

class BusinessTypeRepository extends BaseRepository {

    public $modelName = BusinessType::class;

    public function getBusinessTypeList($homeShow = 0) {
        $data = $this->model->where('status', 1)->with('businessTypeDatas')->get();
    //     if ($homeShow == 1) {
    //         $packages->where('home_show', $homeShow);
    //     }
        return $data;
    }
}
