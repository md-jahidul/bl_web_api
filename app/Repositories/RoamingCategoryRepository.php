<?php

/**
 * User: Bulbul Mahmud Nito
 * Date: 24/02/2020
 */

namespace App\Repositories;

use App\Models\RoamingCategory;

class RoamingCategoryRepository extends BaseRepository
{
    public $modelName = RoamingCategory::class;

    public function getCategoryList() {
        $categories = $this->model
                        ->where('status', 1)
                        ->orderBy('sort')->get();

        return $categories;
    }

}
