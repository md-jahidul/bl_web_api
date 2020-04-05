<?php

namespace App\Services;

use App\Repositories\ProductBookmarkRepository;
use App\Traits\CrudTrait;

class ProductBookmarkService
{
    use CrudTrait;

    /**
     * @var ProductBookmarkRepository
     */
    protected $productBookmarkRepository;

    /**
     * ProductDetailService constructor.
     * @param ProductBookmarkRepository $productBookmarkRepository
     */
    public function __construct(ProductBookmarkRepository $productBookmarkRepository)
    {
        $this->productBookmarkRepository = $productBookmarkRepository;
        $this->setActionRepository($productBookmarkRepository);
    }

    public function appServiceProducts()
    {
        //
    }


}
