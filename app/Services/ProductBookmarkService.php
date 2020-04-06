<?php

namespace App\Services;

use App\Enums\HttpStatusCode;
use App\Repositories\ProductBookmarkRepository;
use App\Traits\CrudTrait;

class ProductBookmarkService extends ApiBaseService {

    use CrudTrait;

    /**
     * @var ProductBookmarkRepository
     */
    protected $productBookmarkRepository;

    /**
     * ProductDetailService constructor.
     * @param ProductBookmarkRepository $productBookmarkRepository
     */
    public function __construct(ProductBookmarkRepository $productBookmarkRepository) {
        $this->productBookmarkRepository = $productBookmarkRepository;
        $this->setActionRepository($productBookmarkRepository);
    }

    public function appServiceProducts($request) {

        $data = $this->productBookmarkRepository->getAppAndService($mobile);

        $response = [];
        $tabCount = 0;
        $tabName = "";
        $count = 0;
        foreach ($data as $k => $val) {

            if ($k == 0) {
                $tabName = $val->alias;
            }

            if ($tabName != $val->alias) {
                $tabName = $val->alias;
                $tabCount++;
                $count = 0;
            }

            $response[$tabCount]['category'] = $val->alias;
            $response[$tabCount]['category_en'] = $val->tab_en;
            $response[$tabCount]['category_bn'] = $val->tab_bn;
            $response[$tabCount]['data'][$count] = $val;
            $count++;
        }

        return $response;
    }

    public function businessProducts($request) {
        $idpData = $this->_getIdpData($request);
        
        if ($idpData->token_status != 'Valid') {
            
            return $this->sendErrorResponse("Token Invalid", [], HttpStatusCode::UNAUTHORIZED);
            
        } else {
            $mobile = $idpData->user->mobile;
            $data = $this->productBookmarkRepository->getBusiness($mobile);

            $response = [];
            $count = 0;
            foreach ($data as $val) {

                $response[0]['category'] = 'internet';
                $response[0]['category_en'] = $val->cat_en;
                $response[0]['category_bn'] = $val->cat_bn;
                $response[0]['data'][$count] = $val;
                $count++;
            }

            return $response;
            return $this->sendSuccessResponse($response, 'Business bookmark data');
        }
    }

    public function offerProducts($request) {

        $data = $this->productBookmarkRepository->getOffers($mobile);
        $response = [];
        $tabCount = 0;
        $tabName = "";
        $count = 0;
        foreach ($data['products'] as $k => $val) {

            if ($k == 0) {
                $tabName = $val->bookmark_category;
            }

            if ($tabName != $val->bookmark_category) {
                $tabName = $val->bookmark_category;
                $tabCount++;
                $count = 0;
            }

            $response['offers'][$tabCount]['category'] = $val->bookmark_category;
            $response['offers'][$tabCount]['category_en'] = $val->cat_en;
            $response['offers'][$tabCount]['category_bn'] = $val->cat_bn;
            $response['offers'][$tabCount]['sim_type'] = $val->sim_alias;
            $response['offers'][$tabCount]['category_type'] = $val->cat_alias;
            $response['offers'][$tabCount]['data'][$count] = $val;
            $count++;
        }

        $rbCount = 0;
        foreach ($data['roming_bundle_offers'] as $k => $val) {

            $response['roming_bundle_offers'][0]['category'] = $val->bookmark_category;
            $response['roming_bundle_offers'][0]['data'][$rbCount] = $val;
            $rbCount++;
        }

        $roCount = 0;
        foreach ($data['roaming_others_offers'] as $k => $val) {

            $response['roaming_others_offers'][0]['category'] = $val->bookmark_category;
            $response['roaming_others_offers'][0]['data'][$roCount] = $val;
            $roCount++;
        }

        $riCount = 0;
        foreach ($data['roaming_info_tips'] as $k => $val) {

            $response['roaming_info_tips'][0]['category'] = $val->bookmark_category;
            $response['roaming_info_tips'][0]['data'][$riCount] = $val;
            $riCount++;
        }

        return $response;
    }

    private function _getIdpData($request) {
        $bearerToken = ['token' => $request->header('authorization')];

        $response = IdpIntegrationService::tokenValidationRequest($bearerToken);
        $idpData = json_decode($response['data']);

        return $idpData;
    }

}
