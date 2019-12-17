<?php

/**
 * Created by PhpStorm.
 * User: BS23
 * Date: 27-Aug-19
 * Time: 3:51 PM
 */

namespace App\Repositories;

use App\Models\AboutPage;
use App\Models\Prize;

class AboutPageRepository extends BaseRepository
{
    public $modelName = AboutPage::class;

    public function findDetail($key)
    {
        return $this->model->where('slug', $key)->get();
    }
}
