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
use App\Services\ImageFileViewerService;
use Illuminate\Http\Response;

class BusinessPackageService {


    /**
     * @var $packageRepo
     */
    protected $packageRepo;
    protected $featureRepo;
    protected $asgnFeatureRepo;
    protected $relatedProductRepo;
    protected $businessHomeService;
    protected $imageFileViewerService;
    public $responseFormatter;

    /**
     * BusinessPackageService constructor.
     * @param BusinessPackageRepository $packageRepo
     * @param BusinessFeaturesRepository $featureRepo
     * @param BusinessAssignedFeaturesRepository $asgnFeatureRepo
     * @param BusinessRelatedProductRepository $relatedProductRepo
     * @param BusinessHomeService $businessHomeService
     * @param ImageFileViewerService $imageFileViewerService
     */
    public function __construct(
        ApiBaseService $responseFormatter,
        BusinessPackageRepository $packageRepo,
        BusinessFeaturesRepository $featureRepo,
        BusinessAssignedFeaturesRepository $asgnFeatureRepo,
        BusinessRelatedProductRepository $relatedProductRepo,
        BusinessHomeService $businessHomeService,
        ImageFileViewerService $imageFileViewerService
    ) {
        $this->packageRepo = $packageRepo;
        $this->featureRepo = $featureRepo;
        $this->asgnFeatureRepo = $asgnFeatureRepo;
        $this->relatedProductRepo = $relatedProductRepo;
        $this->businessHomeService = $businessHomeService;
        $this->imageFileViewerService = $imageFileViewerService;
        $this->responseFormatter = $responseFormatter;
    }

    /**
     * Get business product categories
     * @return Response
     */
    public function getPackages()
    {
        $response = $this->businessHomeService->getPackageList();
        return $this->responseFormatter->sendSuccessResponse($response, 'Business Package List');
    }


    /**
     * Get business package by id
     * @return Response
     */
    public function getPackageBySlug($packageSlug) {
        $package = $this->packageRepo->getPackageById($packageSlug);
        $data = [];
        if (!empty($package)) {
            $keyData = config('filesystems.moduleType.BusinessPackageDetails');
            $imgData = $this->imageFileViewerService->prepareImageData($package, $keyData);
            unset($package->banner_photo, $package->banner_image_mobile, $package->banner_name, $package->banner_name_bn);

            $data['packageDetails'] = array_merge($package->toArray(), $imgData);
            $data['packageDetails']['slug'] = 'packages';

        }

        $data['feature'] = $this->_getFeaturesByPackage($data['packageDetails']['id']);

        $parentType = 1;
        $data['relatedPackages'] = $this->relatedProductRepo->getPackageRelatedProduct($data['packageDetails']['id'], $parentType);

        return $this->responseFormatter->sendSuccessResponse($data, 'Business Package Details');
    }

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
