<?php

namespace App\Services\Assetlite;

//use App\Repositories\AppServiceProductegoryRepository;

use App\Repositories\AppServiceProductDetailsRepository;
use App\Repositories\ComponentRepository;
use App\Repositories\ProductDetailsSectionRepository;
use App\Traits\CrudTrait;
use App\Traits\FileTrait;
use Exception;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;

class ProductDetailsSectionService
{
    use CrudTrait;
    /**
     * @var $productDetailsSectionRepository
     */
    protected $productDetailsSectionRepository;

    /**
     * @var $componentRepository
     */
    protected $componentRepository;


    /**
     * ProductDetailsSectionService constructor.
     * @param ProductDetailsSectionRepository $productDetailsSectionRepository
     * @param ComponentRepository $componentRepository
     */
    public function __construct(
        ProductDetailsSectionRepository $productDetailsSectionRepository,
        ComponentRepository $componentRepository
    ) {
        $this->productDetailsSectionRepository = $productDetailsSectionRepository;
        $this->componentRepository = $componentRepository;
        $this->setActionRepository($productDetailsSectionRepository);
    }


    public function productDetails($productId)
    {
        $sections = $this->productDetailsSectionRepository->findByProperties(['product_id' => $productId]);

        foreach ($sections as $section){
            $components[] = $this->componentRepository->findOneByProperties(['section_details_id' => $section->id]);
        }

        $data = [];
        foreach ($sections as $category => $pack) {
            $data [] = [
                'section' => [
                    $pack
                ],
                'component' => $components,
                'related_product' => null
            ];
        }
        return $data;
    }

}
