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
     * RoamingService constructor.
     * @param RoamingCategoryRepository $catRepo
     * @param RoamingGeneralPageRepository $gnPageRepo
     * @param RoamingOperatorRepository $operatorRepo
     * @param RoamingOfferRepository $offerRepo
     * @param RoamingInfoRepository $infoRepo
     */
    public function __construct(
    ApiBaseService $responseFormatter, RoamingCategoryRepository $catRepo, RoamingGeneralPageRepository $gnPageRepo, RoamingOperatorRepository $operatorRepo, RoamingOfferRepository $offerRepo, RoamingInfoRepository $infoRepo
    ) {
        $this->catRepo = $catRepo;
        $this->gnPageRepo = $gnPageRepo;
        $this->operatorRepo = $operatorRepo;
        $this->offerRepo = $offerRepo;
        $this->infoRepo = $infoRepo;
        $this->responseFormatter = $responseFormatter;
    }

    /**
     * Get roaming categories
     * @return Response
     */
    public function getCategories() {
        $response = $this->catRepo->getCategoryList();
        return $this->responseFormatter->sendSuccessResponse($response, 'Roaming Category List');
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
    public function otherOfferDetalis($offerId) {
        $response = $this->offerRepo->getOtherOffersDetails($offerId);
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
    public function infoTipsDetails($infoId) {
        $response = $this->infoRepo->getInfoDetails($infoId);
        return $this->responseFormatter->sendSuccessResponse($response, 'Roaming Info & Tips Details');
    }

}
