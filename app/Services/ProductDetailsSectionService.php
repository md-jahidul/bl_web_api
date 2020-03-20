<?php

namespace App\Services\Assetlite;

//use App\Repositories\AppServiceProductegoryRepository;

use App\Enums\HttpStatusCode;
use App\Models\Product;
use App\Repositories\AppServiceProductDetailsRepository;
use App\Repositories\BannerImgRelatedProductRepository;
use App\Repositories\ComponentRepository;
use App\Repositories\ProductDetailsSectionRepository;
use App\Services\ApiBaseService;
use App\Traits\CrudTrait;
use App\Traits\FileTrait;
use Exception;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;

class ProductDetailsSectionService extends ApiBaseService
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
     * @var BannerImgRelatedProductRepository
     */
    private $bannerImgRelatedProductRepository;


    /**
     * ProductDetailsSectionService constructor.
     * @param ProductDetailsSectionRepository $productDetailsSectionRepository
     * @param BannerImgRelatedProductRepository $bannerImgRelatedProductRepository
     * @param ComponentRepository $componentRepository
     */
    public function __construct(
        ProductDetailsSectionRepository $productDetailsSectionRepository,
        BannerImgRelatedProductRepository $bannerImgRelatedProductRepository,
        ComponentRepository $componentRepository
    ) {
        $this->productDetailsSectionRepository = $productDetailsSectionRepository;
        $this->bannerImgRelatedProductRepository = $bannerImgRelatedProductRepository;
        $this->componentRepository = $componentRepository;
        $this->setActionRepository($productDetailsSectionRepository);
    }

    public function bindDynamicValues($obj, $json_data = 'other_attributes', $data = null)
    {
        if (!empty($obj->{$json_data})) {
            foreach ($obj->{$json_data} as $key => $value) {
                $obj->{$key} = $value;
            }
            unset($obj->{$json_data});
        }
        // Product Core Data BindDynamicValues
        $data = json_decode($data);

        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $obj->{$key} = $value;
            }
            return $obj;
        }
    }


    public function productDetails($productId)
    {
        $sections = $this->productDetailsSectionRepository->section($productId);

        foreach ($sections as $section){
            ($section->section_type == "tab_section") ? $isTab = true : $isTab = false;
        }

        $bannerRelatedData = $this->bannerImgRelatedProductRepository->findOneByProperties(['product_id' => $productId]);
        $products = [];
        if (isset($bannerRelatedData->related_product_id)){
            foreach ($bannerRelatedData->related_product_id as $id){
                $data = Product::where('id', $id)->productCore()->first();
                array_push($products, $data);
            }
        }
        if ($products) {
            foreach ($products as $product) {
                $data = $product->productCore;
                $this->bindDynamicValues($product, 'offer_info', $data);
                unset($product->productCore);
            }
        }

        $data['header'] = [
            "banner_image" => isset($bannerRelatedData->banner_image_url) ? $bannerRelatedData->banner_image_url : null,
            "alt_text" => isset($bannerRelatedData->alt_text) ? $bannerRelatedData->alt_text : null,
            "isTab" => isset($isTab) ? $isTab : null
        ];

        foreach ($sections as $category => $section) {
            $data['section'] = $sections;
        }
        $data['footer'] = [
            'related_products' => $products
        ];
        return $this->sendSuccessResponse($data, 'Product details page', [], HttpStatusCode::SUCCESS);
    }

}
