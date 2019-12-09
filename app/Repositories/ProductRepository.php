<?php
namespace App\Repositories;

use App\Models\Product;
use Carbon\Carbon;

class ProductRepository extends BaseRepository
{
    /**
     * @var string
     */
    public $modelName = Product::class;

    /**
     * @param $type
     * @return mixed
     */
    public function simTypeProduct($type)
    {
        return $this->model->where('status', 1)
            ->productCore()
            ->startEndDate()
            ->category($type)
            ->get();
    }

    public function showTrendingProduct()
    {
       return $this->model->where('show_in_home', 1)
            ->productCore()
            ->where('status', 1)
            ->startEndDate()
            ->orderBy('display_order')
            ->get();
    }

    /**
     * @param $type
     * @param $id
     * @return mixed
     */
    public function detailProducts($type, $id)
    {
        return $data = $this->model->where('id', $id)
            ->category($type)
            ->productCore()
            ->with('product_details', 'related_product', 'other_related_product')
            ->first();
    }

    public function rechargeOffers()
    {
        return $this->model->where('is_recharge', 1)
            ->productCore()
            ->where('status', 1)
            ->get();
    }

    public function relatedProducts($id)
    {
        return $this->model->where('id', $id)
            ->productCore()
            ->first();
    }

}
