<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Services\Assetlite\ComponentService;
use App\Services\Assetlite\ProductDetailsSectionService;
use Illuminate\Http\Request;

class ProductDetailsController extends Controller
{

    /**
     * @var $componentService
     */
    protected $componentService;
    /**
     * @var ProductDetailsSectionService
     */
    private $productDetailsSectionService;

    /**
     * ProductDetailsSectionService constructor.
     * @param ProductDetailsSectionService $productDetailsSectionService
     * @param ComponentService $componentService
     */
    public function __construct(
        ProductDetailsSectionService $productDetailsSectionService,
        ComponentService $componentService
    ) {
        $this->productDetailsSectionService = $productDetailsSectionService;
        $this->componentService = $componentService;
    }

    public function productDetails($productId)
    {
       return $this->productDetailsSectionService->productDetails($productId);
    }
}
