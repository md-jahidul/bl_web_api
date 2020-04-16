<?php

/**
 * Created by PhpStorm.
 * User: BS23
 * Date: 26-Aug-19
 * Time: 4:31 PM
 */

namespace App\Services;

use App\Enums\HttpStatusCode;
use App\Repositories\OfferCategoryRepository;
use App\Repositories\ProductRepository;
use App\Traits\CrudTrait;
use App\Traits\FileTrait;
use Exception;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;

class OfferCategoryService extends ApiBaseService
{
    /**
     * @var $offerCategoryRepository
     */
    protected $offerCategoryRepository;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * OfferCategoryService constructor.
     * @param OfferCategoryRepository $offerCategoryRepository
     * @param ProductRepository $productRepository
     */
    public function __construct(OfferCategoryRepository $offerCategoryRepository, ProductRepository $productRepository)
    {
        $this->offerCategoryRepository = $offerCategoryRepository;
        $this->productRepository = $productRepository;
    }

    public function bindDynamicValues($obj, $json_data = 'offer_info', $data = null)
    {



        if (!empty($obj->{$json_data})) {
            foreach ($obj->{$json_data} as $key => $value) {
//                dd($value);
                $obj->{$key} = $value;
            }
            unset($obj->{$json_data});
        }
        // Product Core Data BindDynamicValues
        $data = json_decode($obj->productCore, true);


        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $obj->{$key} = $value;
            }
            unset($obj->productCore);
            return $obj;
        }
    }

    /**
     * @param $type
     * @return mixed
     */
    public function relatedProducts($type)
    {
        $packageData = $this->offerCategoryRepository->findOneByProperties(['alias' => 'packages']);
        $packageProduct = [];
        // check package type exist
        $productIds = isset($packageData['other_attributes'][$type.'_related_product_id']) ? $packageData['other_attributes'][$type.'_related_product_id'] : null;
        if ($productIds){
            foreach ($productIds as $productId){
                $product = $this->productRepository->findOneProduct($type, $productId);
                array_push($packageProduct, $product);
                $this->bindDynamicValues($product, 'offer_info', $product->productCore);
            }
        }
        return $this->sendSuccessResponse( $packageProduct, 'Package related products!', [], [],HttpStatusCode::SUCCESS);
    }
}
