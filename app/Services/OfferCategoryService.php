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
use App\Repositories\AlBannerRepository;
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
     * @var AlBannerService
     */
    private $alBannerService;

    /**
     * OfferCategoryService constructor.
     * @param OfferCategoryRepository $offerCategoryRepository
     * @param ProductRepository $productRepository
     * @param ImageFileViewerService $fileViewerService
     */
    public function __construct(
        OfferCategoryRepository $offerCategoryRepository,
        ProductRepository $productRepository,
        ImageFileViewerService $fileViewerService,
        AlBannerService $alBannerService
    ) {
        $this->offerCategoryRepository = $offerCategoryRepository;
        $this->productRepository = $productRepository;
        $this->fileViewerService = $fileViewerService;
        $this->alBannerService = $alBannerService;
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
        $tags = TagCategory::all(['id', 'name_en', 'name_bn', 'alias', 'tag_color']);
        $sim = SimCategory::all(['id', 'name', 'alias']);
        $offer = OfferCategory::where('status', 1)
            ->where('parent_id', 0)
            ->with(['children' => function($q){
                $q->where('status', 1);
            }])
            ->get();
        $offer->makeHidden(['created_at', 'updated_at', 'created_by', 'updated_by']);
//        if (!empty($offer)) {
//            $offer_final = array_map(function($value) {
//                if (!empty($value['banner_image_url'])) {

//                    $encrypted = base64_encode($value['banner_image_url']);
//
//                    $extension = explode('.', $value['banner_image_url']);
//                    $extension = isset($extension[1]) ? ".".$extension[1] : null;
//                    $fileName = $value['banner_alt_text'] . $extension;
//
//                    $model = "OfferCategory";


//                    $value['banner_image_url'] = request()->root() . "/$model/$fileName";
//                    $value['banner_image_url'] = request()->root() . "banner-image/web/$model/{fileName}". "/api/v1/show-file/$encrypted/" . $fileName;
//                    $value['banner_image_url'] = $value['banner_image_url'];
//                }
//                if (!empty($value['banner_image_mobile'])) {
//                    $value['banner_image_mobile'] = $value['banner_image_mobile'];
//                }
//                return $value;
//            }, $offer->toArray());
//        } else {
//            $offer_final = [];
//        }

        $duration = DurationCategory::all();

        $banner = $this->alBannerService->getBanner(0, 'amar_offer');

        $amarOfferBanner[] = [
            'banner_image_url' => $banner->image ?? null,
            'postpaid_banner_image_url' => null,
            'url_slug' => 'amar-offer',
            'url_slug_bn' => 'amar-offer',
        ];

        $offerData = array_merge($offer->toArray(), $amarOfferBanner);

        $data[] = [
                'tag' => $tags,
                'sim' => $sim,
                'offer' => $offerData,
                'duration' => $duration
            ];

        return $this->sendSuccessResponse($data, 'Offer Categories');
    }
}
