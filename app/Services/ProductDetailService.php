<?php

namespace App\Services;

use App\Models\OtherRelatedProduct;
use App\Models\RelatedProduct;
use App\Repositories\PartnerOfferDetailRepository;
use App\Repositories\ProductDetailRepository;
use App\Traits\CrudTrait;


class ProductDetailService
{
    use CrudTrait;

    /**
     * @var $partnerOfferDetailRepository
     */
    protected $productDetailRepository;

    /**
     * ProductDetailService constructor.
     * @param ProductDetailRepository $productDetailRepository
     */
    public function __construct(ProductDetailRepository $productDetailRepository)
    {
        $this->productDetailRepository = $productDetailRepository;
        $this->setActionRepository($productDetailRepository);
    }



}
