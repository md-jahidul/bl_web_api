<?php

namespace App\Services;

use App\Repositories\ProductRepository;
use App\Traits\CrudTrait;
use Illuminate\Database\QueryException;

class ProductService extends ApiBaseService
{
    use CrudTrait;

    /**
     * @var $partnerOfferRepository
     */
    protected $productRepository;

    /***
     * ProductService constructor.
     * @param ProductRepository $productRepository
     */
    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
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
    public function simTypeOffers($type)
    {
        try {
            $products = $this->productRepository->simTypeProduct($type);

            if ($products) {
                foreach ($products as $product) {
                    $this->bindDynamicValues($product, 'offer_info');
                }
                return response()->success($products, 'Data Found!');
            }
            return response()->error("Data Not Found!");

        } catch (QueryException $exception) {
            return response()->error("Data Not Found!", $exception);
        }
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

}
