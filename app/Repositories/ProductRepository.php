<?php
namespace App\Repositories;

use App\Models\Product;

class ProductRepository extends BaseRepository
{
    /**
     * @var string
     */
    public $modelName = Product::class;

    /**
     * @param $type
     * @param $id
     * @return mixed
     */
    public function detailProducts($type, $id)
    {
        return $this->model->where('id', $id)
            ->category($type)
            ->with('product_details', 'related_product', 'other_related_product')
            ->first();
    }

}