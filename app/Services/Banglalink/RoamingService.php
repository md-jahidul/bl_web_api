<?php

/**
 * User: Bulbul Mahmud Nito
 * Date: 23/03/2020
 */

namespace App\Services\Banglalink;

use App\Models\RoamingInfoComponents;
use App\Models\RoamingOtherOfferComponents;
use App\Services\ApiBaseService;
use App\Repositories\RoamingCategoryRepository;
use App\Repositories\RoamingOperatorRepository;
use App\Repositories\RoamingGeneralPageRepository;
use App\Repositories\RoamingOfferRepository;
use App\Repositories\RoamingInfoRepository;
use App\Services\ImageFileViewerService;
use Illuminate\Http\Response;


class RoamingService {

    /**
     * @var $catRepo
     * @var $gnPageRepo
     * @var $operatorRepo
     * @var $offerRepo
     * @var $infoRepo
     */
    protected $catRepo;
    protected $gnPageRepo;
    protected $operatorRepo;
    protected $offerRepo;
    protected $infoRepo;
    public $responseFormatter;

    /**
     * @var $imageFileViewerService
     */

    private $imageFileViewerService;

    /**
     * RoamingService constructor.
     * @param RoamingCategoryRepository $catRepo
     * @param RoamingGeneralPageRepository $gnPageRepo
     * @param RoamingOperatorRepository $operatorRepo
     * @param RoamingOfferRepository $offerRepo
     * @param RoamingInfoRepository $infoRepo
     * @param ImageFileViewerService $imageFileViewerService
     */
    public function __construct(
        ApiBaseService $responseFormatter,
        RoamingCategoryRepository $catRepo,
        RoamingGeneralPageRepository $gnPageRepo,
        RoamingOperatorRepository $operatorRepo,
        RoamingOfferRepository $offerRepo,
        RoamingInfoRepository $infoRepo,
        ImageFileViewerService $imageFileViewerService
    ) {
        $this->catRepo = $catRepo;
        $this->gnPageRepo = $gnPageRepo;
        $this->operatorRepo = $operatorRepo;
        $this->offerRepo = $offerRepo;
        $this->infoRepo = $infoRepo;
        $this->imageFileViewerService = $imageFileViewerService;
        $this->responseFormatter = $responseFormatter;
    }

    /**
     * Get roaming categories
     * @return Response
     */
    public function getCategories()
    {
        $categories = $this->catRepo->getCategoryList();
        $data = [];
        $count = 0;

        $slugs = array(
            1 => 'offer',
            2 => 'about-roaming',
            3 => 'roaming-rates',
            4 => 'bill-payment',
            5 => 'info-tips',
        );

        $keyData = config('filesystems.moduleType.RoamingCategory');


        foreach ($categories as $v) {
            $data[$count]['id'] = $v->id;
            $data[$count]['category_slug'] = $slugs[$v->id];
            $data[$count]['url_slug'] = $v->url_slug;
            $data[$count]['url_slug_bn'] = $v->url_slug_bn;
            $data[$count]['page_header'] = $v->page_header;
            $data[$count]['page_header_bn'] = $v->page_header_bn;
            $data[$count]['schema_markup'] = $v->schema_markup;
            $data[$count]['name_en'] = $v->name_en;
            $data[$count]['name_bn'] = $v->name_bn;
            $data[$count]['alt_text'] = $v->alt_text;
            $data[$count]['alt_text_bn'] = $v->alt_text_bn;
            $imgData = $this->imageFileViewerService->prepareImageData($v, $keyData);
            $data[$count] = array_merge($data[$count], $imgData);

            $count++;
        }

        return $this->responseFormatter->sendSuccessResponse($data, 'Roaming Category List');
    }

    /**
     * Get roaming categories
     * @return Response
     */
    public function roamingGeneralPage($pageSlug) {
        $response = $this->gnPageRepo->roamingGeneralPage($pageSlug);
        return $this->responseFormatter->sendSuccessResponse($response, 'Roaming ' . $pageSlug . ' Page');
    }

    /**
     * Get roaming country
     * @return Response
     */
    public function getCountries() {
        $response = $this->operatorRepo->getCountries();
        return $this->responseFormatter->sendSuccessResponse($response, 'Roaming Country List');
    }

    /**
     * Get roaming operators by country name
     * @return Response
     */
    public function getOperators($countryEn) {
        $response = $this->operatorRepo->getOperators($countryEn);
        return $this->responseFormatter->sendSuccessResponse($response, 'Roaming Operator List');
    }

    /**
     * Get roaming other offers
     * @return Response
     */
    public function offerPage() {
        $response = $this->offerRepo->getOtherOffers();
        return $this->responseFormatter->sendSuccessResponse($response, 'Roaming Other Offers');
    }

    /**
     * Get roaming other offer details
     * @return Response
     */
    public function otherOfferDetalis($offerSlug) {
        $offer = $this->offerRepo->getOtherOffersDetails($offerSlug);

        $data = [];
        $keyData = config('filesystems.moduleType.RoamingOtherOffer');

        $data['name_en'] = $offer->name_en;
        $data['name_bn'] = $offer->name_bn;
        $data['short_text_en'] = $offer->short_text_en;
        $data['short_text_bn'] = $offer->short_text_bn;
        $data['alt_text'] = $offer->alt_text;
        $data['alt_text_bn'] = $offer->alt_text_bn;
        $data['url_slug'] = $offer->url_slug;
        $data['url_slug_bn'] = $offer->url_slug_bn;
        $data['page_header'] = $offer->page_header;
        $data['page_header_bn'] = $offer->page_header_bn;
        $data['schema_markup'] = $offer->schema_markup;
        $data['likes'] = $offer->likes;
        $imgData = $this->imageFileViewerService->prepareImageData($offer, $keyData);
        $data = array_merge($data, $imgData);

        $components = RoamingOtherOfferComponents::where('parent_id', $offer->id)->orderBy('position')->get();
        $data['components'] = [];
        foreach ($components as $k => $val) {

            $textEn = json_decode($val->body_text_en);
            $textBn = json_decode($val->body_text_bn);

            $data['components'][$k]['component_type'] = $val->component_type;
            $data['components'][$k]['data_en'] = $textEn;
            $data['components'][$k]['data_bn'] = $textBn;
        }

        $data['details_en'] = $offer->details_en;
        $data['details_bn'] = $offer->details_en;

        return $this->responseFormatter->sendSuccessResponse($data, 'Roaming Other Offer Details');
    }

    /**
     * Get roaming rates and bundle
     * @return Response
     */
    public function ratesAndBundle($country, $operator) {
        $response = $this->offerRepo->ratesAndBundle($country, $operator);
        $response['operatorInstruction'] = $this->operatorRepo->getSingleOperator($operator);
        return $this->responseFormatter->sendSuccessResponse($response, 'Roaming Rates & Bundle');
    }

    /**
     * Like roaming bundle
     * @return Response
     */
    public function bundleLike($bundleId) {
        $response = $this->offerRepo->bundleLike($bundleId);
        return $this->responseFormatter->sendSuccessResponse($response, 'Roaming Bundle Like');
    }

    /**
     * Like roaming offer
     * @return Response
     */
    public function otherOfferLike($offerId) {
        $response = $this->offerRepo->otherOfferLike($offerId);
        return $this->responseFormatter->sendSuccessResponse($response, 'Roaming Offer Like');
    }

    /**
     * Get roaming rates page data
     * @return Response
     */
    public function roamingRates() {
        $response = $this->offerRepo->roamingRates();
        return $this->responseFormatter->sendSuccessResponse($response, 'Roaming Rates Page');
    }

    /**
     * Get roaming other offers
     * @return Response
     */
    public function infoTips() {
        $response = $this->infoRepo->getInfoTips();
        return $this->responseFormatter->sendSuccessResponse($response, 'Roaming Info & Tips');
    }

    /**
     * Get roaming other offer details
     * @return Response
     */
    public function infoTipsDetails($infoSlug) {
        $info = $this->infoRepo->getInfoDetails($infoSlug);

        $data = [];
        $keyData = config('filesystems.moduleType.RoamingInfo');

        $data['name_en'] = $info->name_en;
        $data['name_bn'] = $info->name_bn;
        $data['short_text_en'] = $info->short_text_en;
        $data['short_text_bn'] = $info->short_text_bn;
        $data['url_slug'] = $info->url_slug;
        $data['url_slug_bn'] = $info->url_slug_bn;
        $data['alt_text'] = $info->alt_text;
        $data['alt_text_bn'] = $info->alt_text_bn;
        $data['page_header'] = $info->page_header;
        $data['page_header_bn'] = $info->page_header_bn;
        $data['schema_markup'] = $info->schema_markup;
        $data['likes'] = $info->likes;
        $imgData = $this->imageFileViewerService->prepareImageData($info, $keyData);
        $data = array_merge($data, $imgData);

        $components = RoamingInfoComponents::where('parent_id', $info->id)->orderBy('position')->get();
        $data['components'] = [];
        foreach ($components as $k => $val) {

            $textEn = json_decode($val->body_text_en);
            $textBn = json_decode($val->body_text_bn);

            $data['components'][$k]['component_type'] = $val->component_type;
            $data['components'][$k]['data_en'] = $textEn;
            $data['components'][$k]['data_bn'] = $textBn;

        }

        return $this->responseFormatter->sendSuccessResponse($data, 'Roaming Info & Tips Details');
    }

}
