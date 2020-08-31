<?php

/**
 * Created by PhpStorm.
 * User: BS23
 * Date: 27-Aug-19
 * Time: 3:51 PM
 */

namespace App\Repositories;

use App\Models\LeadProduct;

class LeadProductRepository extends BaseRepository
{
    public $modelName = LeadProduct::class;

    public function getProduct()
    {
        return $this->model->select('id', 'title as name')
            ->get();
    }
}
