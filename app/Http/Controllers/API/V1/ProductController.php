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
use App\Models\AlProductBookmark;
use App\Models\Product;
use App\Models\ProductBookmark;
use App\Services\Banglalink\BanglalinkProductService;
use App\Services\Banglalink\PurchaseService;
use App\Services\CustomerService;
use App\Services\ProductDetailService;
use App\Services\ProductService;
use Carbon\Carbon;
use http\Exception\InvalidArgumentException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;

class ProductController extends Controller
{
    /**
     * @var ProductService
     * @var ProductDetailService
     */
    private $productService;
    private $productDetailService;

    /**
     * @var CustomerService;
     */
    private $customerService;

    /**
     * @var PurchaseService
     */
    private $purchaseService;

    /**
     * @var BanglalinkProductService
     */
    private $blProductService;

    /**
     * ProductController constructor.
     * @param ProductService $productService
     * @param ProductDetailService $productDetailService
     * @param PurchaseService $purchaseService
     * @param BanglalinkProductService $blProductService
     */
    public function __construct(
        ProductService $productService,
        ProductDetailService $productDetailService,
        PurchaseService $purchaseService,
        BanglalinkProductService $blProductService,
        CustomerService $customerService
    )
    {
        $this->productService = $productService;
        $this->productDetailService = $productDetailService;
        $this->purchaseService = $purchaseService;
        $this->blProductService = $blProductService;
        $this->customerService = $customerService;
    }

    /**
     * @param Request $request
     * @param $type
     * @return mixed
     * @throws \Illuminate\Auth\AuthenticationException
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

    public function saveProduct(Request $request)
    {
//        dd($request->all());

        $validator = Validator::make($request->all(), ['product_code' => 'required', 'operation_type' => 'required']);
        if ($validator->fails()) {
            return response()->json($validator->messages(), HttpStatusCode::VALIDATION_ERROR);
        }
        return $this->productService->customerProductSave($request);
    }


    public function productLike($productId)
    {
        try {
            $products = Product::where('product_code', $productId)->first();
            if ($products) {
                $products['like'] = $products['like'] + 1;
                $products->update();
            }
        } catch (QueryException $exception) {
            return response()->error("Data Not Found!", $exception);
        }
    }

    public function customerLoanProducts(Request $request)
    {
        $customerId = 8494; //TODO:Implement real time customer id based on token
        return $this->productService->getCustomerLoanProducts($customerId);
    }
}
