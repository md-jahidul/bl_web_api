<?php

/**
 * User: Bulbul Mahmud Nito
 * Date: 23/03/2020
 */

namespace App\Services\Banglalink;

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
            $data = array_merge($data, $imgData);

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
        $response = $this->offerRepo->getOtherOffersDetails($offerSlug);
        return $this->responseFormatter->sendSuccessResponse($response, 'Roaming Other Offer Details');
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
        $response = $this->infoRepo->getInfoDetails($infoSlug);
        return $this->responseFormatter->sendSuccessResponse($response, 'Roaming Info & Tips Details');
    }

}
