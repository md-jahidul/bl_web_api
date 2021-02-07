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
use App\Services\ImageFileViewerService;
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
     * @var ImageFileViewerService
     */
    private $fileViewerService;


    /**
     * ProductDetailsSectionService constructor.
     * @param ProductDetailsSectionRepository $productDetailsSectionRepository
     * @param BannerImgRelatedProductRepository $bannerImgRelatedProductRepository
     * @param ComponentRepository $componentRepository
     * @param ImageFileViewerService $fileViewerService
     */
    public function __construct(
        ProductDetailsSectionRepository $productDetailsSectionRepository,
        BannerImgRelatedProductRepository $bannerImgRelatedProductRepository,
        ComponentRepository $componentRepository,
        ImageFileViewerService $fileViewerService
    ) {
        $this->productDetailsSectionRepository = $productDetailsSectionRepository;
        $this->bannerImgRelatedProductRepository = $bannerImgRelatedProductRepository;
        $this->componentRepository = $componentRepository;
        $this->fileViewerService = $fileViewerService;
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

//        dd($sections);

        $sectionCollection = collect($sections)->map(function ($section){
            $componentCollection = collect($section['components'])->map(function ($component){
                $componentMulti = collect($component['componentMultiData'])->map(function ($componentMultiData){
                    $keyData = config('filesystems.moduleType.OfferOtherMultiComponent');
                    $fileViewer = $this->fileViewerService->prepareImageData($componentMultiData, $keyData);
                    return [
                        "component_id" => $componentMultiData->component_id,
                        "page_type" => $componentMultiData->page_type,
                        "title_en" => $componentMultiData->title_en,
                        "title_bn" => $componentMultiData->title_bn,
                        "details_en" => $componentMultiData->details_en,
                        "details_bn" => $componentMultiData->details_bn,
                        "alt_text_en" => $componentMultiData->alt_text_en,
                        "alt_text_bn" => $componentMultiData->alt_text_bn,
                        "image_url_en" => isset($fileViewer["image_url_en"]) ? $fileViewer["image_url_en"] : null,
                        "image_url_bn" => isset($fileViewer['image_url_bn']) ? $fileViewer['image_url_bn'] : null,
                    ];
                });
                return [
                    "id" => $component->id,
                    "section_details_id" => $component->section_details_id,
                    "page_type" => $component->page_type,
                    "title_en" => $component->title_en,
                    "title_bn" => $component->title_bn,
                    "slug" => $component->slug,
                    "description_en" => $component->description_en,
                    "description_bn" => $component->description_bn,
                    "editor_en" => $component->editor_en,
                    "editor_bn" => $component->editor_bn,
                    "video" => $component->video,
                    "alt_links" => $component->alt_links,
                    "button_en" => $component->button_en,
                    "button_bn" => $component->button_bn,
                    "button_link" => $component->button_link,
                    "offer_type_id" => $component->offer_type_id,
                    "offer_type" => $component->offer_type,
                    "extra_title_bn" => $component->extra_title_bn,
                    "extra_title_en" => $component->extra_title_en,
                    "component_type" => $component->component_type,
                    "is_default" => $component->is_default,
                    "other_attributes" => $component->other_attributes,
                    "multiple_attributes" => $componentMulti,
                ];
            });

            $keyData = config('filesystems.moduleType.OfferOtherDetailsTab');
            $fileViewer = $this->fileViewerService->prepareImageData($section, $keyData);
            return [
                "id" => $section->id,
                "product_id" => $section->product_id,
                "section_type" => $section->section_type,
                "title_en" => $section->title_en,
                "title_bn" => $section->title_bn,
                'banner_image_web_en' => isset($fileViewer["banner_image_web_en"]) ? $fileViewer["banner_image_web_en"] : null,
                'banner_image_web_bn' => isset($fileViewer['banner_image_web_bn']) ? $fileViewer['banner_image_web_bn'] : null,
                'banner_image_mobile_en' => isset($fileViewer["banner_image_mobile_en"]) ? $fileViewer["banner_image_mobile_en"] : null,
                'banner_image_mobile_bn' => isset($fileViewer['banner_image_mobile_bn']) ? $fileViewer['banner_image_mobile_bn'] : null,
                "alt_text" => $section->alt_text,
                "alt_text_bn" => $section->alt_text_bn,
                'components' => $componentCollection
            ];
        });

        $data['section'] = $sectionCollection;
        $data['related_products'] = $products;
        return $this->sendSuccessResponse($data, 'Product details page', [], HttpStatusCode::SUCCESS);
    }

}
