<?php

/**
 * Created by PhpStorm.
 * User: BS23
 * Date: 27-Aug-19
 * Time: 3:51 PM
 */

namespace App\Repositories;

use App\Models\ProductDetailsSection;

class ProductDetailsSectionRepository extends BaseRepository
{
    public $modelName = ProductDetailsSection::class;

    public function section($productId)
    {
        return $this->model->where('product_id', $productId)
            ->with(['components' => function($q){
                $q->where('page_type', 'product_details');
            }])
            ->get();
    }
}
