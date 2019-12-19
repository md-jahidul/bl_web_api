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
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
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
     * @param CustomerService $customerService
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
     * @throws AuthenticationException
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
        return $this->productService->getProductCodesByCustomerId($customerId);
    }

    /**
     * @param Request $request
     * @return JsonResponse|void
     * @throws AuthenticationException
     */
    public function bookmarkProduct(Request $request)
    {
        $validator = Validator::make($request->all(), ['product_code' => 'required', 'operation_type' => 'required']);
        if ($validator->fails()) {
            return response()->json($validator->messages(), HttpStatusCode::VALIDATION_ERROR);
        }
        return $this->productService->customerProductBookmark($request);
    }

    /**
     * @param $productId
     * @return JsonResponse
     */
    public function productLike($productId)
    {
        return $this->productService->like($productId);
    }

    public function customerLoanProducts(Request $request)
    {
        $customer = $this->customerService->getCustomerDetails($request);
        $customerId = $customer->customer_account_id;
//        $customerId = 8494;
        return $this->productService->getCustomerLoanProducts($customerId);
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \App\Exceptions\IdpAuthException
     */
    public function rechargeOffers(Request $request)
    {
        return $this->productService->allRechargeOffers($request);
    }

    public function rechargeOfferByAmount($amount)
    {
        return $this->productService->rechargeOfferByAmount($amount);
    }

    public function getCustomerBookmarkProducts(Request $request)
    {
        return $this->productService->findCustomerSaveProducts($request);
    }

    public function customerSavedBookmarkProduct(Request $request)
    {
        return $this->productService->findCustomerProducts($request);
    }
}
