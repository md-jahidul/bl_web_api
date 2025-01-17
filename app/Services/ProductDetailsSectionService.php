<?php

namespace App\Services\Assetlite;

//use App\Repositories\AppServiceProductegoryRepository;

use App\Enums\HttpStatusCode;
use App\Enums\OfferType;
use App\Models\OfferCategory;
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
use PhpParser\Node\Stmt\DeclareDeclare;

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


    public function productDetails($slug)
    {
        $parentProduct = Product::where('url_slug', $slug)->orWhere('url_slug_bn', $slug)
            ->select(
                'id',
                'product_code',
                'url_slug',
                'url_slug_bn',
                'schema_markup',
                'page_header',
                'offer_category_id',
                'name_en',
                'name_bn',
                'ussd_bn',
                'call_rate_unit_bn',
                'balance_check_ussd_bn',
                'like',
                'offer_info')
            ->productCore()->first();
        if ($parentProduct){
            $this->bindDynamicValues($parentProduct, 'offer_info', $parentProduct->productCore);
            unset($parentProduct->productCore);
        }

        $offerTypeId = isset($parentProduct->package_offer_type_id) ? $parentProduct->package_offer_type_id : null;

        $offerType = OfferCategory::where('id', $offerTypeId)->select('id', 'name_en', 'alias')->first();

        $sections = $this->productDetailsSectionRepository->section($parentProduct->id);

        foreach ($sections as $section){
            ($section->section_type == "tab_section") ? $isTab = true : $isTab = false;
        }

        $bannerRelatedData = $this->bannerImgRelatedProductRepository->findOneByProperties(['product_id' => $parentProduct->id]);
        $products = [];
        if (isset($bannerRelatedData->related_product_id)){
            foreach ($bannerRelatedData->related_product_id as $id){
                $data = Product::where('id', $id)
                    ->select(
                        'id', 'product_code',
                        'tag_category_id',
                        'sim_category_id',
                        'offer_category_id',
                        'special_product',
                        'name_en', 'name_bn',
                        'ussd_bn', 'call_rate_unit_bn',
                        'balance_check_ussd_bn', 'like')
                    ->productCore()->first();
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
            "banner_mobile_view" => isset($bannerRelatedData->mobile_view_img_url) ? $bannerRelatedData->mobile_view_img_url : null,
            "alt_text" => isset($bannerRelatedData->alt_text) ? $bannerRelatedData->alt_text : null,
            "isTab" => isset($isTab) ? $isTab : null,
            "product_type" => isset($offerType) ? $offerType->alias : null
        ];

        $data['product'] = $parentProduct;

        foreach ($sections as $category => $section) {
            $data['section'] = $sections;
        }
        $data['related_products'] = $products;
        return $this->sendSuccessResponse($data, 'Product details page', [], HttpStatusCode::SUCCESS);
    }

}
