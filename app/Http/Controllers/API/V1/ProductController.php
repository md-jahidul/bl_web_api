<?php
/**
 * Created by PhpStorm.
 * User: bs-23-jahidul
 * Date: 11/25/19
 * Time: 6:43 PM
 */

namespace App\Http\Controllers\API\V1;

use App\Enums\HttpStatusCode;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\Banglalink\BanglalinkProductService;
use App\Services\Banglalink\PurchaseService;
use App\Services\ProductDetailService;
use App\Services\ProductService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * @var ProductService
     * @var ProductDetailService
     */
    private $productService;
    private $productDetailService;

    /**
     * @var PurchaseService
     */
    private $purchaseService;

    /**
     * @var BanglalinkProductService
     */
    private $blProductService;

    /***
     * ProductController constructor.
     * @param ProductService $productService
     * @param ProductDetailService $productDetailService
     */
    public function __construct(
        ProductService $productService,
        ProductDetailService $productDetailService,
        PurchaseService $purchaseService,
         BanglalinkProductService $blProductService

    )
    {
        $this->productService = $productService;
        $this->productDetailService = $productDetailService;
        $this->purchaseService = $purchaseService;
        $this->blProductService = $blProductService;
    }

    /**
     * @param $type
     * @return mixed
     */
    public function simPackageOffers(Request $request, $type)
    {
        return $this->productService->simTypeOffers($type, $request);
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

    public function purchase(Request $request)
    {
        $validator = Validator::make($request->all(), ['product_code' => 'required']);
        if ($validator->fails()) {
            return response()->json($validator->messages(), HttpStatusCode::VALIDATION_ERROR);
        }

        return $this->purchaseService->purchaseItem($request);
    }

    public function getProducts($customerId)
    {
        return $this->productService->getProductCodesByCustomerId(8479);
    }
}
