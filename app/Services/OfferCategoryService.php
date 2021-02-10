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
        $offer = $this->offerCategoryRepository->categories();



        if (!empty($offer)) {
            $keyData = config('filesystems.moduleType.OfferCategory');
            $keyDataPostpaid = config('filesystems.moduleType.OfferCategoryPostpaid');
            $offer_final = array_map(function($value) use ($keyData, $keyDataPostpaid) {

                $imgData = $this->fileViewerService->prepareImageData($value, $keyData);
                $imgDataPostpaid = $this->fileViewerService->prepareImageData($value, $keyDataPostpaid);
                $postpaidBanner = [
                    "postpaid_banner_image_web_en" => $imgDataPostpaid['banner_image_web_en'] ?? null,
                    "postpaid_banner_image_web_bn" => $imgDataPostpaid['banner_image_web_bn'] ?? null,
                    "postpaid_banner_image_mobile_en" => $imgDataPostpaid['banner_image_mobile_en'] ?? null,
                    "postpaid_banner_image_mobile_bn" => $imgDataPostpaid['banner_image_mobile_bn'] ?? null,
                ];

                $allTypesBanner = array_merge($imgData, $postpaidBanner);
                $value = array_merge($value, $allTypesBanner);
                unset(
                    $value['banner_image_url'], $value['banner_image_mobile'],
                    $value['banner_name'], $value['banner_name_bn'],
                    $value['postpaid_banner_image_url'], $value['postpaid_banner_image_mobile'],
                    $value['postpaid_banner_name'], $value['postpaid_banner_name_bn']
                );
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
