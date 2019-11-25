<?php
/**
 * Created by PhpStorm.
 * User: bs-23-jahidul
 * Date: 11/25/19
 * Time: 6:43 PM
 */

namespace App\Http\Controllers\API\V1;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ProductDetailService;
use App\Services\ProductService;

class ProductController extends Controller
{
    /**
     * @var ProductService
     * @var ProductDetailService
     */
    private $productService;
    private $productDetailService;

    /***
     * ProductController constructor.
     * @param ProductService $productService
     * @param ProductDetailService $productDetailService
     */
    public function __construct(
        ProductService $productService,
        ProductDetailService $productDetailService

    ) {
        $this->productService = $productService;
        $this->productDetailService = $productDetailService;
    }

    /**
     * @param $type
     * @param $id
     * @return mixed
     */
    public function productDetails($type, $id)
    {
       return $productDetail = $this->productService->details($type, $id);
    }
}