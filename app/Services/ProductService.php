<?php

namespace App\Services;


use App\Models\ProductCore;
use App\Http\Resources\ProductCoreResource;
use App\Repositories\ProductBookmarkRepository;
use App\Repositories\ProductRepository;
use App\Services\Banglalink\BanglalinkLoanService;
use App\Services\Banglalink\BanglalinkProductService;
use App\Traits\CrudTrait;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;

class ProductService extends ApiBaseService
{
    use CrudTrait;

    /**
     * @var $partnerOfferRepository
     */
    protected $productRepository;

    /**
     * @var BanglalinkProductService
     */
    protected $blProductService;

    /**
     * @var CustomerService
     */
    protected $customerService;

    /**
     * @var BanglalinkLoanService
     */
    protected $blLoanProductService;

    /***
     * @var ProductBookmarkRepository
     */
    protected $productBookmarkRepository;

    /**
     * ProductService constructor.
     * @param ProductRepository $productRepository
     * @param BanglalinkProductService $blProductService
     * @param CustomerService $customerService
     * @param ProductBookmarkRepository $productBookmarkRepository
     * @param BanglalinkLoanService $blLoanProductService
     */
    public function __construct
    (
        ProductRepository $productRepository,
        BanglalinkProductService $blProductService,
        CustomerService $customerService,
        ProductBookmarkRepository $productBookmarkRepository,
        BanglalinkLoanService $blLoanProductService
    )
    {
        $this->productRepository = $productRepository;
        $this->blProductService = $blProductService;
        $this->customerService = $customerService;
        $this->productBookmarkRepository = $productBookmarkRepository;
        $this->blLoanProductService = $blLoanProductService;
        $this->setActionRepository($productRepository);
    }

    /**
     * @param $obj
     * @param string $json_data
     * In PHP, By default objects are passed as reference copy to a new Object.
     * @param null $data
     */
    public function bindDynamicValues($obj, $json_data = 'other_attributes', $data = null)
    {
        if (!empty($obj->{$json_data})) {
            foreach ($obj->{$json_data} as $key => $value) {
                $obj->{$key} = $value;
            }
        }
        // Product Core Data BindDynamicValues
        $data = json_decode($data);
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $obj->{$key} = $value;
            }
        }
        unset($obj->{$json_data});
    }

    /**
     * @param $products
     * @return array
     */
    public function findRelatedProduct($products)
    {
        $data = [];
        foreach ($products as $product) {
            $findProduct = $this->findOne($product->related_product_id);
            array_push($data, $findProduct);
        }
        return $data;
    }

    public function trendingProduct()
    {
        $products = $this->productRepository->showTrendingProduct();
        foreach ($products as $product) {
            $this->bindDynamicValues($product, 'offer_info', $product->productCore);
            unset($product->productCore);
        }
        $products = ProductCoreResource::collection($products);
        return $products;
    }

    /**
     * @param $type
     * @param $request
     * @return mixed
     * @throws AuthenticationException
     */
    public function simTypeOffers($type, $request)
    {
        try {
            $products = $this->productRepository->simTypeProduct($type);
            $viewAbleProducts = $products;

            if ($this->isUserLoggedIn($request)) {
                $customer = $this->customerService->getCustomerDetails($request);
                $availableProducts = $this->getProductCodesByCustomerId($customer->customer_account_id);
                $viewAbleProducts = $this->filterProductsByUser($viewAbleProducts, $availableProducts);
            }

            if ($viewAbleProducts) {
                foreach ($viewAbleProducts as $product) {
                    $data = $product->productCore;
                    $this->bindDynamicValues($product, 'offer_info', $data);
                    unset($product->productCore);
                }
//                $viewAbleProducts = ProductCoreResource::collection(collect($viewAbleProducts));

                return response()->success($viewAbleProducts, 'Data Found!');
            }
            return response()->error("Data Not Found!");
        } catch (QueryException $exception) {
            return response()->error("Data Not Found!", $exception);
        }
    }

    private function filterProductsByUser($allProducts, $availableProductIds)
    {
        $viewableProducts = [];
        foreach ($allProducts as $product) {
            if (in_array($product->product_code, $availableProductIds)) {
                array_push($viewableProducts, $product);
            }
        }
        return $viewableProducts;
    }

    private function isUserLoggedIn($request)
    {
        if ($request->header('authorization')) {
            return true;
        }
        return false;
    }

    /**
     * @param $type
     * @param $id
     * @return mixed
     */
    public function details($type, $id)
    {
        try {

            $productDetail = $this->productRepository->detailProducts($type, $id);

            if ($productDetail) {
                $this->bindDynamicValues($productDetail, 'offer_info');

                $productDetail->other_related_products = $this->findRelatedProduct($productDetail->other_related_product);
                $productDetail->related_products = $this->findRelatedProduct($productDetail->related_product);

                $this->bindDynamicValues($productDetail->related_products, 'offer_info');

                unset($productDetail->other_related_product);
                unset($productDetail->related_product);

                return response()->success($productDetail, 'Data Found!');
            }

            return response()->error("Data Not Found!");

        } catch (QueryException $exception) {
            return response()->error("Data Not Found!", $exception);
        }
    }

    public function getProductCodesByCustomerId($customerId)
    {
        $customerProducts = $this->blProductService->getCustomerProducts($customerId);
        $productIds = [];

        foreach ($customerProducts as $product) {
            array_push($productIds, $product['code']);
        }
        return $productIds;
    }

    /**
     * @param $request
     * @return JsonResponse
     * @throws AuthenticationException
     */
    public function customerProductBookmark($request)
    {
        $customerInfo = $this->customerService->getCustomerDetails($request);

        $operationType = $request->operation_type;
        $productCode = $request->product_code;

        if ($operationType == 'save') {
            $this->productBookmarkRepository->save([
                'mobile' => $customerInfo->phone,
                'product_code' => $productCode,
            ]);
            return $this->sendSuccessResponse([], 'Bookmark saved successfully!');

        } else if ($operationType == 'delete') {
            $this->productRepository->delete();
            return $this->sendSuccessResponse([], 'Bookmark removed successfully!');
        }
        return $this->sendErrorResponse('Invalid operation');
    }

    public function getCustomerLoanProducts($customerId)
    {
        $availableLoanProducts = [];
        $loanProducts = $this->blLoanProductService->getCustomerLoanProducts($customerId);
        foreach ($loanProducts as $loan) {
            $product = ProductCore::where('product_code', $loan['code'])->first();
            if ($product)
                array_push($availableLoanProducts, $product);
        }

        return $this->sendSuccessResponse($availableLoanProducts, 'Available loan products');
    }

    /**
     * @param $type
     * @return mixed
     */
    public function allRechargeOffers($type)
    {
        //TODO:Filter by user while logged in
        try {
            $rechargeOffers = $this->productRepository->rechargeOffers($type);
            if ($rechargeOffers) {
                foreach ($rechargeOffers as $product) {
                    $data = $product->productCore;
                    $this->bindDynamicValues($product, 'offer_info', $data);
                    unset($product->productCore);
                }

                return response()->success($rechargeOffers, 'Data Found!');
            }
            return response()->error("Data Not Found!");

        } catch (QueryException $exception) {
            return response()->error("Something is Wrong!", $exception);
        }
    }

    /**
     * @param $request
     * @return mixed
     * @throws AuthenticationException
     */
    public function findCustomerSaveProducts($request)
    {
        $customerInfo = $this->customerService->getCustomerDetails($request);
        $bookmarkProduct = $this->productBookmarkRepository->findByProperties(['mobile' => $customerInfo->phone]);
        if ($bookmarkProduct) {
            return response()->success($bookmarkProduct, 'Data Found!');
        }
        return response()->error("Data Not Found!");
    }
}
