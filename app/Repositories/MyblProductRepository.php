<?php

namespace App\Repositories;
use App\Models\MyBlProduct;

class MyblProductRepository extends BaseRepository
{
    protected $modelName = MyBlProduct::class;

    public function getProduct($productCode) 
    {
        return $this->findBy(['product_code' => $productCode], 'details');
    }
    
    public function getProductDetail() 
    {
        return $this->model->details;
    }
}
