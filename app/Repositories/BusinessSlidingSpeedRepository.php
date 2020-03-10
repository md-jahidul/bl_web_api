<?php

/**
 * User: Bulbul Mahmud Nito
 * Date: 10/03/2020
 */

namespace App\Repositories;

use App\Models\BusinessSlidingSpeed;

class BusinessSlidingSpeedRepository extends BaseRepository {

    public $modelName = BusinessSlidingSpeed::class;

    public function getSpeeds() {
        $speed = $this->model->first();
            $data['enterprise_sliding_speed'] = $speed->enterprise_speed;
            $data['news_sliding_speed'] = $speed->news_speed;
        return $data;
    }


}
