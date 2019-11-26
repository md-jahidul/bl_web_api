<?php

/**
 * Created by PhpStorm.
 * User: bs-205
 * Date: 18/08/19
 * Time: 17:07
 */

namespace App\Repositories;

use App\Models\ProductDetail;


class ProductDetailRepository extends BaseRepository
{
    /**
     * @var string
     */
    public $modelName = ProductDetail::class;

}
