<?php

/**
 * User: Bulbul Mahmud Nito
 * Date: 13/02/2020
 */

namespace App\Services\Banglalink;

use App\Models\TagCategory;
use App\Services\ApiBaseService;
use App\Repositories\BusinessInternetRepository;
use App\Services\ImageFileViewerService;
use Illuminate\Http\Response;

class BusinessInternetService {


    /**
     * @var $internetRepo
     */
    protected $internetRepo;
    protected $imageFileViewerService;
    public $responseFormatter;

    /**
     * BusinessInternetService constructor.
     * @param BusinessInternetRepository $internetRepo
     * @param ImageFileViewerService $imageFileViewerService
     */
    public function __construct(
        ApiBaseService $responseFormatter,
        BusinessInternetRepository $internetRepo,
        ImageFileViewerService $imageFileViewerService
    ) {
        $this->imageFileViewerService = $imageFileViewerService;
        $this->internetRepo = $internetRepo;
        $this->responseFormatter = $responseFormatter;
    }

    /**
     * Get Internet package
     * @return Response
     */
    public function getInternetPackage() {
        $response = $this->internetRepo->getInternetPackageList();
        return $this->responseFormatter->sendSuccessResponse($response, 'Business Internet Package List');
    }

    /**
     * Get Internet package details
     * @return Response
     */
    public function getInternetDetails($internetSlug)
    {
        $product = $this->internetRepo->getInternetPackageDetails($internetSlug);
        $internet = $product['internet'];
        $relatedProduct = $product['relatedProduct'];
        $keyData = config('filesystems.moduleType.BusinessInternet');

        $imgData = $this->imageFileViewerService->prepareImageData($internet, $keyData);

        $dataVol = $internet->data_volume;
        if ($internet->volume_data_unit == "GB") {
            $dataVol = $internet->data_volume * 1024;
        }

        $data['id'] = $internet->id;
        $data['type'] = $internet->type;
        $data['product_code'] = $internet->product_code;
        $data['product_name'] = $internet->product_name;
        $data['internet_volume_mb'] = $dataVol;
        $data['volume_data_unit'] = $internet->volume_data_unit;
        $data['validity_days'] = $internet->validity;
        $data['validity_unit'] = $internet->validity_unit;
        $data['price_tk'] = $internet->mrp;
        $data['activation_ussd_code'] = $internet->activation_ussd_code;
        $data['balance_check_ussd_code'] = $internet->balance_check_ussd_code;
        $data['alt_text'] = $internet->alt_text;
        $data['alt_text_bn'] = $internet->alt_text_bn;
        $data['package_details_en'] = $internet->package_details_en;
        $data['package_details_bn'] = $internet->package_details_bn;
        $data['url_slug'] = $internet->url_slug;
        $data['url_slug_bn'] = $internet->url_slug_bn;
        $data['page_header'] = $internet->page_header;
        $data['page_header_bn'] = $internet->page_header_bn;
        $data['schema_markup'] = $internet->schema_markup;
        $data['likes'] = $internet->likes;

        $data['tag_en'] = "";
        $data['tag_bn'] = "";
        $data['tag_color'] = "";
        $tags = TagCategory::where("id", $internet->tag_id)->first();
        if (!empty($tags)) {
            $data['tag_en'] = $tags->name_en;
            $data['tag_bn'] = $tags->name_bn;
            $data['tag_color'] = $tags->tag_color;
        }

        $data = array_merge($data, $imgData);

        $count = 0;
        $data['related_product'] = [];
        foreach ($relatedProduct as $rp) {

            $rpDataVol = $rp->data_volume;
            if ($rp->volume_data_unit == "GB") {
                $rpDataVol = $rp->data_volume * 1024;
            }

            $data['related_product'][$count]['id'] = $rp->id;
            $data['related_product'][$count]['type'] = $rp->type;
            $data['related_product'][$count]['product_code'] = $rp->product_code;
            $data['related_product'][$count]['product_name'] = $rp->product_name;
            $data['related_product'][$count]['internet_volume_mb'] = $rpDataVol;
            $data['related_product'][$count]['volume_data_unit'] = $rp->volume_data_unit;
            $data['related_product'][$count]['validity_days'] = $rp->validity;
            $data['related_product'][$count]['validity_unit'] = $rp->validity_unit;
            $data['related_product'][$count]['price_tk'] = $rp->mrp;
            $data['related_product'][$count]['activation_ussd_code'] = $rp->activation_ussd_code;
            $data['related_product'][$count]['balance_check_ussd_code'] = $rp->balance_check_ussd_code;
            $data['related_product'][$count]['url_slug'] = $rp->url_slug;
            $data['related_product'][$count]['url_slug_bn'] = $rp->url_slug_bn;
            $data['related_product'][$count]['likes'] = $rp->likes;

            $data['related_product'][$count]['tag_en'] = "";
            $data['related_product'][$count]['tag_bn'] = "";
            $data['related_product'][$count]['tag_color'] = "";
            $rpTags = TagCategory::where("id", $rp->tag_id)->first();
            if (!empty($rpTags)) {
                $data['related_product'][$count]['tag_en'] = $rpTags->name_en;
                $data['related_product'][$count]['tag_bn'] = $rpTags->name_bn;
                $data['related_product'][$count]['tag_color'] = $rpTags->tag_color;
            }

            $count++;
        }

        return $this->responseFormatter->sendSuccessResponse($data, 'Business Internet Package Details');
    }
    /**
     * Give Internet like and get total count
     * @return Response
     */
    public function saveInternetLike($internetId) {
        $response = $this->internetRepo->internetLike($internetId);
        return $this->responseFormatter->sendSuccessResponse($response, 'Business Internet Package Likes');
    }



}
