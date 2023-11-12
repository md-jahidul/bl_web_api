<?php

/**
 * User: Bulbul Mahmud Nito
 * Date: 13/02/2020
 */

namespace App\Services\Banglalink;
use App\Services\ApiBaseService;

use App\Repositories\BusinessPackageRepository;
use App\Repositories\BusinessFeaturesRepository;
use App\Repositories\BusinessAssignedFeaturesRepository;
use App\Repositories\BusinessRelatedProductRepository;
use App\Repositories\ComponentRepository;
use Illuminate\Http\Response;

class BusinessPackageService {


    public const PAGE_TYPE = "business_package";
    public const PAGE_DETAILS_TYPE = "business_package_details";

    /**
     * @var $packageRepo
     */
    protected $packageRepo;
    protected $featureRepo;
    protected $asgnFeatureRepo;
    protected $relatedProductRepo;
    protected $componentRepo;
    public $responseFormatter;

    /**
     * BusinessPackageService constructor.
     * @param BusinessPackageRepository $packageRepo
     * @param BusinessFeaturesRepository $featureRepo
     * @param BusinessAssignedFeaturesRepository $asgnFeatureRepo
     * @param BusinessRelatedProductRepository $relatedProductRepo
     */
    public function __construct(ApiBaseService $responseFormatter, BusinessPackageRepository $packageRepo,
            BusinessFeaturesRepository $featureRepo, BusinessAssignedFeaturesRepository $asgnFeatureRepo,
            BusinessRelatedProductRepository $relatedProductRepo, ComponentRepository $componentRepo) {
        $this->packageRepo = $packageRepo;
        $this->featureRepo = $featureRepo;
        $this->asgnFeatureRepo = $asgnFeatureRepo;
        $this->relatedProductRepo = $relatedProductRepo;
        $this->responseFormatter = $responseFormatter;
        $this->componentRepo = $componentRepo;
    }

//    /**
//     * Get business product categories
//     * @return Response
//     */
//    public function getPackages() {
//        $response['packages'] = $this->packageRepo->getPackageList();
//        $response['components'] = $this->componentRepo->getComponentByPageType(self::PAGE_TYPE);
//        return $this->responseFormatter->sendSuccessResponse($response, 'Business Package List');
//    }


//    /**
//     * Get business package by id
//     * @return Response
//     */
//    public function getPackageBySlug($packageSlug) {
//        $data['packageDetails'] = $this->packageRepo->getPackageById($packageSlug);
//
//        $data['feature'] = $this->_getFeaturesByPackage($data['packageDetails']['id']);
//
//        $parentType = 1;
//        $data['relatedPackages'] = $this->relatedProductRepo->getPackageRelatedProduct($data['packageDetails']['id'], $parentType);
//
//        $data['components'] = $this->componentRepo->findBy(['section_details_id'=> $data['packageDetails']['id'], 'page_type' => self::PAGE_DETAILS_TYPE, 'status' => 1],null, [
//                'id', 'section_details_id', 'page_type',
//                'component_type', 'title_en', 'title_bn',
//                'editor_en', 'editor_bn', 'extra_title_bn',
//                'extra_title_en', 'multiple_attributes',
//                'video', 'image', 'alt_text', 'other_attributes'
//        ]);
//
//
//        return $this->responseFormatter->sendSuccessResponse($data, 'Business Package Details');
//    }

    /**
     * Get business package by id
     * @return Response
     */
    private function _getFeaturesByPackage($packageId) {
        $parentType = 1; //parent type 1 for package
        $response = $this->asgnFeatureRepo->getAssignedFeatures($packageId, $parentType);
        return $response;
    }



}
