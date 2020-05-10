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
            ->where('status', 1)
            ->with(['components' => function($q){
                $q->orderBy('component_order', 'ASC')
                    ->where('page_type', 'product_details')
                    ->where('status', 1)
                    ->with(['productInfo' => function ($productInfo){
                        $productInfo->select(
                            'id', 'product_code',
                            'name_en', 'name_bn',
                            'ussd_bn', 'call_rate_unit_bn',
                            'balance_check_ussd_bn', 'like');
                    }]);

            }])
            ->get();
    }
}
