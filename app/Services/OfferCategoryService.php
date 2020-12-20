<?php

/**
 * Created by PhpStorm.
 * User: BS23
 * Date: 26-Aug-19
 * Time: 4:31 PM
 */

namespace App\Services;

use App\Enums\HttpStatusCode;
use App\Models\DurationCategory;
use App\Models\OfferCategory;
use App\Models\SimCategory;
use App\Models\TagCategory;
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
     * @var ImageFileViewerService
     */
    private $fileViewerService;

    /**
     * OfferCategoryService constructor.
     * @param OfferCategoryRepository $offerCategoryRepository
     * @param ProductRepository $productRepository
     * @param ImageFileViewerService $fileViewerService
     */
    public function __construct(
        OfferCategoryRepository $offerCategoryRepository,
        ProductRepository $productRepository,
        ImageFileViewerService $fileViewerService
    ) {
        $this->offerCategoryRepository = $offerCategoryRepository;
        $this->productRepository = $productRepository;
        $this->fileViewerService = $fileViewerService;
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

    public function offerCatList()
    {
        $tags = TagCategory::all();
        $sim = SimCategory::all();
        $offer = $this->offerCategoryRepository->findByProperties(['parent_id' => 0], [
            'id', 'parent_id', 'name_en', 'name_bn', 'alias', 'banner_alt_text', 'banner_alt_text_bn', 'url_slug', 'url_slug_bn',
            'banner_name', 'banner_name_bn', 'schema_markup', 'page_header', 'page_header_bn', 'banner_image_url', 'banner_image_mobile'
        ]);

        if (!empty($offer)) {
            $offer_final = array_map(function($value) {

                $extension = explode('.', $value['banner_image_url']);
                $extension = isset($extension[1]) ? ".".$extension[1] : null;
                $fileNameEn = $value['banner_name'] . $extension;
                $fileNameBn = $value['banner_name_bn'] . $extension;
                $model = "offer-category";

                if (!empty($value['banner_image_url'])) {
                    $bannerType = "banner-web";
                    $value['banner_image_url_en'] = "/$bannerType/$model/$fileNameEn";
                    $value['banner_image_url_bn'] = "/$bannerType/$model/$fileNameBn";
                }
                if (!empty($value['banner_image_mobile'])) {
                    $bannerType = "banner-mobile";
                    $value['banner_image_mobile_en'] = "/$bannerType/$model/$fileNameEn";
                    $value['banner_image_mobile_bn'] = "/$bannerType/$model/$fileNameBn";
                }
                unset($value['banner_image_url'], $value['banner_image_mobile']);
                return $value;
            }, $offer->toArray());
        } else {
            $offer_final = [];
        }
        $duration = DurationCategory::all();


        $data[] = [
                'tag' => $tags,
                'sim' => $sim,
                'offer' => $offer_final,
                'duration' => $duration
            ];
        return $this->sendSuccessResponse($data, 'Offer Categories');
    }
}
