<?php

namespace App\Services;

use App\Http\Resources\PartnerOfferResource;
use App\Models\PartnerCategory;
use App\Repositories\PartnerOfferRepository;
use App\Repositories\ProductDetailRepository;
use App\Repositories\ProductRepository;
use App\Traits\CrudTrait;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\QueryException;
use Illuminate\Http\Response;
use phpDocumentor\Reflection\Types\Null_;

class PartnerOfferService extends ApiBaseService
{
    use CrudTrait;

    /**
     * @var $partnerOfferRepository
     */
    protected $partnerOfferRepository;

    /**
     * PartnerOfferService constructor.
     * @param PartnerOfferRepository $partnerOfferRepository
     */
    public function __construct(PartnerOfferRepository $partnerOfferRepository)
    {
        $this->partnerOfferRepository = $partnerOfferRepository;
        $this->setActionRepository($partnerOfferRepository);
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
     * @Get_Priyojon_Offers form Partner table
     */
    public function priyojonOffers()
    {

        try {
            $partnerOffers = $this->partnerOfferRepository->offers();

            if ($partnerOffers) {
                $partnerOffers = PartnerOfferResource::collection($partnerOffers);
                return response()->success($partnerOffers, 'Data Found!');
            }
            return response()->error("Data Not Found!");

        } catch (QueryException $exception) {
            return response()->error("Something wrong", $exception);
        }
    }


    /**
     * @param $type
     * @param $id
     * @return mixed
     */
//    public function details($type, $id)
//    {
//        try {
//
//            $productDetail = $this->productRepository->detailProducts($type, $id);
//
//            if ($productDetail) {
//                $this->bindDynamicValues($productDetail, 'offer_info');
//
//                $productDetail->other_related_products = $this->findRelatedProduct($productDetail->other_related_product);
//                $productDetail->related_products = $this->findRelatedProduct($productDetail->related_product);
//
//                $this->bindDynamicValues($productDetail->related_products, 'offer_info');
//
//                unset($productDetail->other_related_product);
//                unset($productDetail->related_product);
//
//                return response()->success($productDetail, 'Data Found!');
//            }
//
//            return response()->error("Data Not Found!");
//
//        } catch (QueryException $exception) {
//            return response()->error("Something wrong", $exception);
//        }
//    }



}
