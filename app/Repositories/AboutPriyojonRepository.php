<?php

/**
 * Created by PhpStorm.
 * User: bs-205
 * Date: 18/08/19
 * Time: 17:07
 */

namespace App\Repositories;

use App\Models\AboutPriyojon;
use App\Models\ProductDetail;


class AboutPriyojonRepository extends BaseRepository
{
    /**
     * @var string
     */
    public $modelName = AboutPriyojon::class;

    public function findDetail($key)
    {
        return $this->model->where('slug', $key)->get();
    }
}
