<?php

/**
 * Created by PhpStorm.
 * User: bs-205
 * Date: 18/08/19
 * Time: 17:07
 */

namespace App\Repositories;

use App\Models\Banner;
use App\Models\BannerImgRelatedProduct;

class BannerImgRelatedProductRepository extends BaseRepository
{
    public $modelName = BannerImgRelatedProduct::class;

    public function updateOrCreate($data, $productId)
    {
        $this->model->updateOrCreate(['product_id' => $productId], $data);
    }
}
