<?php

namespace App\Services\Assetlite;

//use App\Repositories\AppServiceProductegoryRepository;

use App\Repositories\AppServiceProductDetailsRepository;
use App\Repositories\BannerImgRelatedProductRepository;
use App\Repositories\ComponentRepository;
use App\Repositories\ProductDetailsSectionRepository;
use App\Traits\CrudTrait;
use App\Traits\FileTrait;
use Exception;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;

class BannerImgRelatedProductService
{
    use CrudTrait;
    use FileTrait;
    /**
     * @var $productDetailsSectionRepository
     */
    protected $bannerImgRelatedProductRepository;


    /**
     * ProductDetailsSectionService constructor.
     * @param BannerImgRelatedProductRepository $bannerImgRelatedProductRepository
     */
    public function __construct(BannerImgRelatedProductRepository $bannerImgRelatedProductRepository)
    {
        $this->bannerImgRelatedProductRepository = $bannerImgRelatedProductRepository;
        $this->setActionRepository($bannerImgRelatedProductRepository);
    }

    public function findBannerImageRelatedProduct($productId)
    {
        return $this->bannerImgRelatedProductRepository->findOneByProperties(['product_id' => $productId]);
    }

}
