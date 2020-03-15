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
                $q->orderBy('component_order', 'ASC')
                    ->where('page_type', 'product_details')
                    ->with(['productInfo' => function ($productInfo){
//                        $productInfo->select('id', 'product_code', 'rate_cutter_unit');
                    }]);

            }])
            ->get();
    }
}
