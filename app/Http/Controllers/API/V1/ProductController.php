<?php
/**
 * Created by PhpStorm.
 * User: bs-23-jahidul
 * Date: 11/25/19
 * Time: 6:43 PM
 */

namespace App\Http\Controllers\API\V1;

use App\Enums\HttpStatusCode;
use App\Exceptions\IdpAuthException;
use App\Http\Controllers\Controller;
use App\Models\AlProductBookmark;
use App\Models\Product;
use App\Models\ProductBookmark;
use App\Services\Banglalink\BanglalinkProductService;
use App\Services\Banglalink\ProductLoanService;
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
     * @var ProductLoanService
     */
    private $productLoanService;

    /**
     * ProductController constructor.
     * @param ProductService $productService
     * @param ProductLoanService $productLoanService
     * @param ProductDetailService $productDetailService
     * @param PurchaseService $purchaseService
     * @param BanglalinkProductService $blProductService
     * @param CustomerService $customerService
     */
    public function __construct(
        ProductService $productService,
        ProductLoanService $productLoanService,
        ProductDetailService $productDetailService,
        PurchaseService $purchaseService,
        BanglalinkProductService $blProductService,
        CustomerService $customerService
    )
    {
        $this->productService = $productService;
        $this->productLoanService = $productLoanService;
        $this->productDetailService = $productDetailService;
        $this->purchaseService = $purchaseService;
        $this->blProductService = $blProductService;
        $this->customerService = $customerService;
    }

    /**
     * @param Request $request
     * @param $type
     * @param $offerType
     * @return mixed
     * @throws IdpAuthException
     */
    public function simPackageOffers($type, $offerType = null)
    {
        // return $this->productService->simTypeOffers($type, $offerType);
        return $this->productService->simTypeOffersTypeWise($type, $offerType);
    }

    /**
     * @param $type
     * @param $id
     * @return mixed
     */
    public function productDetails($slug)
    {
        return $this->productService->details($slug);
    }

    public function eligibleCheck($mobile, $productCode)
    {
        return $this->productService->eligible($mobile, $productCode);
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
     * @return JsonResponse
     * @throws IdpAuthException
     */
    public function bookmarkProductSaveRemove(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'product_id' => 'required',
                'operation_type' => 'required',
                'module_type' => 'required',
                'category' => 'required'
            ]);
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

    public function customerLoanProducts(Request $request, $loanType, $msisdn)
    {
        return $this->productLoanService->getLoanInfo($request, $loanType, $msisdn);
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws IdpAuthException
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
