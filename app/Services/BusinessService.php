<?php

namespace App\Services;

use App\Http\Resources\BusinessTypeResource;
use App\Repositories\BusinessAssignedFeaturesRepository;
use App\Repositories\BusinessInternetRepository;
use App\Repositories\BusinessOthersRepository;
use App\Repositories\BusinessPackageRepository;
use App\Repositories\BusinessRelatedProductRepository;
use App\Repositories\BusinessTypeRepository;
use App\Repositories\ComponentRepository;
use App\Services\Banglalink\BusinessOthersService;
use Exception;
use Illuminate\Http\Response;

/**
 * Class BusinessTypeService
 * @package App\Services
 */
class BusinessService extends ApiBaseService
{

    /**
     * @var BusinessTypeRepository
     */
    protected $businessTypeRepository;
    /**
     * @var BusinessPackageRepository
     */
    private $businessPackageRepository;
    /**
     * @var BusinessInternetRepository
     */
    private $businessInternetRepository;
    /**
     * @var BusinessOthersRepository
     */
    private $businessOthersRepository;
    /**
     * @var ComponentRepository
     */
    private $componentRepo;

    public const PAGE_TYPE = "business_package";
    public const PAGE_DETAILS_TYPE = "business_package_details";
    /**
     * @var BusinessAssignedFeaturesRepository
     */
    private $asgnFeatureRepo;
    /**
     * @var BusinessRelatedProductRepository
     */
    private $relatedProductRepo;
    /**
     * @var BusinessOthersService
     */
    private $businessOthersService;


    /**
     * BannerService constructor.
     * @param BusinessPackageRepository $businessPackageRepository
     * @param BusinessInternetRepository $businessInternetRepository
     * @param BusinessOthersRepository $businessOthersRepository
     */
    public function __construct(
        BusinessPackageRepository $businessPackageRepository,
        BusinessInternetRepository $businessInternetRepository,
        BusinessOthersRepository $businessOthersRepository,
        ComponentRepository $componentRepo,
        BusinessAssignedFeaturesRepository $asgnFeatureRepo,
        BusinessRelatedProductRepository $relatedProductRepo,
        BusinessOthersService $businessOthersService
    ) {
        $this->businessPackageRepository = $businessPackageRepository;
        $this->businessInternetRepository = $businessInternetRepository;
        $this->businessOthersRepository = $businessOthersRepository;
        $this->componentRepo = $componentRepo;
        $this->asgnFeatureRepo = $asgnFeatureRepo;
        $this->relatedProductRepo = $relatedProductRepo;
        $this->businessOthersService = $businessOthersService;
    }

    /**
     * Request for Banner info
     *
     * @return mixed|string
     */
    public function getBusinessBySlug($slug)
    {
        try {
            if ($slug == "packages") {
                $response['packages'] = $this->businessPackageRepository->getPackageList();
                $response['components'] = $this->componentRepo->getComponentByPageType(self::PAGE_TYPE);
                return $this->sendSuccessResponse($response, 'Business Package List');
            }elseif ($slug == "internet"){
                $response = $this->businessInternetRepository->getInternetPackageList();
                return $this->sendSuccessResponse($response, 'Business Internet Package List');
            }elseif ($slug == "business-solution"){
                $enterpriseSolution = $this->businessOthersRepository->getOtherService($slug);
                return $this->sendSuccessResponse($enterpriseSolution, 'Enterprise Solutions');
            }else {
                return $this->sendErrorResponse('Data Fetch Failed', 'Slug is invalid');
            }
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

    /**
     * Request for Banner info
     *
     * @return mixed|string
     */
    public function getBusinessDetailsBySlug($slug, $urlSlug)
    {
        try {
            if ($slug == "packages") {
                $data['packageDetails'] = $this->businessPackageRepository->getPackageById($urlSlug);

                if (count($data['packageDetails']) > 0) {
                    $data['feature'] = $this->_getFeaturesByPackage($data['packageDetails']['id']);
                    $parentType = 1;
                    $data['relatedPackages'] = $this->relatedProductRepo->getPackageRelatedProduct($data['packageDetails']['id'], $parentType);

                    $data['components'] = $this->componentRepo->findBy(['section_details_id'=> $data['packageDetails']['id'], 'page_type' => self::PAGE_DETAILS_TYPE, 'status' => 1],null, [
                        'id', 'section_details_id', 'page_type',
                        'component_type', 'title_en', 'title_bn',
                        'editor_en', 'editor_bn', 'extra_title_bn',
                        'extra_title_en', 'multiple_attributes',
                        'video', 'image', 'alt_text', 'other_attributes'
                    ]);
                } else {
                    $data = json_decode("{}");
                }

                return $this->sendSuccessResponse($data, 'Business Package Details');
            }elseif ($slug == "internet"){
                $response = $this->businessInternetRepository->getInternetPackageDetails($urlSlug);
                return $this->sendSuccessResponse($response, 'Business Internet Package Details');
            }elseif ($slug == "business-solution"){
                $data = $this->businessOthersService->getServiceBySlug($urlSlug);
                return $this->sendSuccessResponse($data, 'Enterprise Solutions Details');
            }else {
                return $this->sendErrorResponse('Data Fetch Failed', 'Slug is invalid');
            }
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }

    /**
     * Get business package by id
     * @return array
     */
    private function _getFeaturesByPackage($packageId) {
        $parentType = 1; //parent type 1 for package
        return $this->asgnFeatureRepo->getAssignedFeatures($packageId, $parentType);
    }
}
