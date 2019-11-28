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
        $mytime = Carbon::now('Asia/Dhaka');
        $dateTime = $mytime->toDateTimeString();
        $currentSecends = strtotime($dateTime);

        return Product::where('status', 1)
//            ->whereNull('start_date')
//            ->where('start_date', '<=', $currentSecends)
//            ->whereNull('end_date')
//            ->orWhere('end_date', '>=', $currentSecends)
            ->category($type)
            ->get();
    }

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