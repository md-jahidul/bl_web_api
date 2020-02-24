<?php

/**
 * User: Bulbul Mahmud Nito
 * Date: 18/02/2020
 */

namespace App\Services\Banglalink;

use App\Services\ApiBaseService;

use App\Repositories\BusinessOthersRepository;
use App\Repositories\BusinessAssignedFeaturesRepository;
use App\Repositories\BusinessComPhotoTextRepository;
use App\Repositories\BusinessComPkOneRepository;
use App\Repositories\BusinessComPkTwoRepository;
use App\Repositories\BusinessComFeaturesRepository;
use App\Repositories\BusinessComPriceTableRepository;
use App\Repositories\BusinessComVideoRepository;
use App\Repositories\BusinessComPhotoRepository;
use Illuminate\Http\Response;

class BusinessOthersService {



    /**
     * @var $otherRepo
     * @var $asgnFeatureRepo
     */
    protected $otherRepo;
    protected $photoTextRepo;
    protected $pkOneRepo;
    protected $pkTwoRepo;
    protected $featureRepo;
    protected $priceTableRepo;
    protected $videoRepo;
    protected $photoRepo;
    protected $asgnFeatureRepo;
    
    public $responseFormatter;

    /**
     * BusinessPackageService constructor.
     * @param BusinessOthersRepository $otherRepo
     * @param BusinessComPhotoTextRepository $photoTextRepo
     * @param BusinessComPkOneRepository $pkOneRepo
     * @param BusinessComPkTwoRepository $pkTwoRepo
     * @param BusinessComFeaturesRepository $featureRepo
     * @param BusinessComPriceTableRepository $priceTableRepo
     * @param BusinessComVideoRepository $videoRepo
     * @param BusinessComPhotoRepository $photoRepo
     * @param BusinessAssignedFeaturesRepository $asgnFeatureRepo
     */
    public function __construct(
            ApiBaseService $responseFormatter,
    BusinessOthersRepository $otherRepo, BusinessComPhotoTextRepository $photoTextRepo, BusinessComPkOneRepository $pkOneRepo, BusinessComPkTwoRepository $pkTwoRepo, BusinessComFeaturesRepository $featureRepo, BusinessComPriceTableRepository $priceTableRepo, BusinessComVideoRepository $videoRepo, BusinessComPhotoRepository $photoRepo, BusinessAssignedFeaturesRepository $asgnFeatureRepo
    ) {
        $this->otherRepo = $otherRepo;
        $this->photoTextRepo = $photoTextRepo;
        $this->pkOneRepo = $pkOneRepo;
        $this->pkTwoRepo = $pkTwoRepo;
        $this->featureRepo = $featureRepo;
        $this->priceTableRepo = $priceTableRepo;
        $this->videoRepo = $videoRepo;
        $this->photoRepo = $photoRepo;
        $this->asgnFeatureRepo = $asgnFeatureRepo;
        
        $this->responseFormatter = $responseFormatter;
    }

    /**
     * get other service list
     * @return Response
     */
    public function getOtherService($type) {
        $servces = $this->otherRepo->getOtherService($type);
        return $this->responseFormatter->sendSuccessResponse($servces, 'Enterprise Solutions');
    }

    /**
     * Get components by service ID
     * @return Response
     */
    public function getComponents($serviceId) {

        $components = [];
        $photoText = $this->photoTextRepo->getComponent($serviceId);
        foreach ($photoText as $v) {
            $components[$v->position]['type'] = 'Photo with Text';
            $components[$v->position]['text'] = $v->text;
            $components[$v->position]['photo_url'] = $v->photo_url;
        }

        $packageOne = $this->pkOneRepo->getComponent($serviceId);

        foreach ($packageOne as $v) {
            $components[$v->position]['type'] = 'Package Comparison One';
            $components[$v->position]['text'] = $v->heads;
            $components[$v->position]['photo_url'] = "";
        }

        $packageTwo = $this->pkTwoRepo->getComponent($serviceId);

        foreach ($packageTwo as $v) {
            $components[$v->position]['type'] = 'Package Comparison Two';
            $components[$v->position]['text'] = $v->name;
            $components[$v->position]['photo_url'] = "";
        }


        $features = $this->featureRepo->getComponent($serviceId);

        foreach ($features as $v) {
            $components[$v->position]['type'] = 'Product Features';
            $components[$v->position]['text'] = $v->feature_text;
            $components[$v->position]['photo_url'] = "";
        }


        $priceTable = $this->priceTableRepo->getComponent($serviceId);

        foreach ($priceTable as $v) {
            $headArray = json_decode($v->table_head);
            $head = implode(', ', $headArray);
            $components[$v->position]['type'] = 'Product Price Table';
            $components[$v->position]['text'] = $head;
            $components[$v->position]['photo_url'] = "";
        }


        $video = $this->videoRepo->getComponent($serviceId);
        foreach ($video as $v) {
            $components[$v->position]['type'] = 'Video Component';
            $components[$v->position]['text'] = $v->title;
            $components[$v->position]['photo_url'] = "";
        }



        $photos = $this->photoRepo->getComponent($serviceId);

        foreach ($photos as $v) {
            $components[$v->position]['type'] = 'Photo Component';
            $components[$v->position]['text'] = "";
            $components[$v->position]['photo_url'] = "";
            $components[$v->position]['photo1'] = $v->photo_one;
            $components[$v->position]['photo2'] = $v->photo_two;
            $components[$v->position]['photo3'] = $v->photo_three;
            $components[$v->position]['photo4'] = $v->photo_four;
        }

        ksort($components);

        return $components;
    }

  

    /**
     * Get business package by id
     * @return Response
     */
    public function getServiceById($serviceId) {
        $response = $this->otherRepo->getServiceById($serviceId);
        return $response;
    }

    /**
     * Get business package by id
     * @return Response
     */
    public function getFeaturesByService($serviceType, $serviceId) {
        $types = array("business-solusion" => 2, "iot" => 3, "others" => 4);
        $parentType = $types[$serviceType];
        $response = $this->asgnFeatureRepo->getAssignedFeatures($serviceId, $parentType);
        return $response;
    }

}
