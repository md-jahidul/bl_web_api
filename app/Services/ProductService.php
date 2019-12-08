<?php

namespace App\Services;

use App\Repositories\ProductRepository;
use App\Services\Banglalink\BanglalinkProductService;
use App\Traits\CrudTrait;
use Illuminate\Database\QueryException;

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

    /***
     * ProductService constructor.
     * @param ProductRepository $productRepository
     */
    public function __construct(ProductRepository $productRepository, BanglalinkProductService $blProductService,
                                CustomerService $customerService)
    {
        $this->productRepository = $productRepository;
        $this->blProductService = $blProductService;
        $this->customerService = $customerService;
        $this->setActionRepository($productRepository);
    }

    /**
     * @param $obj
     * @param string $json_data
     * In PHP, By default objects are passed as reference copy to a new Object.
     */
    public function bindDynamicValues($obj, $json_data = 'other_attributes')
    {
        if (!empty($obj->{$json_data})) {
            foreach ($obj->{$json_data} as $key => $value) {
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

    /**
     * @param $type
     * @return mixed
     */
    public function simTypeOffers($type, $request)
    {
        try {

            $products = $this->productRepository->simTypeProduct($type);

            $viewAbleProducts = $products;

            if ($this->isUserLoggedIn($request)) {
                $customer = $this->customerService->getCustomerDetails($request);
                $availableProducts = $this->getProductCodesByCustomerId($customer->customer_account_id);
                $viewAbleProducts = $this->filterProductsByUser($availableProducts);
            }

            if ($viewAbleProducts) {
                foreach ($viewAbleProducts as $product) {
                    $this->bindDynamicValues($product, 'offer_info');
                }
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

        return $availableProductIds;
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

        return $this->sendSuccessResponse($productIds, 'Product List');
    }

}
