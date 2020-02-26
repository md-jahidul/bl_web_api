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
    ApiBaseService $responseFormatter, BusinessOthersRepository $otherRepo, BusinessComPhotoTextRepository $photoTextRepo, BusinessComPkOneRepository $pkOneRepo, BusinessComPkTwoRepository $pkTwoRepo, BusinessComFeaturesRepository $featureRepo, BusinessComPriceTableRepository $priceTableRepo, BusinessComVideoRepository $videoRepo, BusinessComPhotoRepository $photoRepo, BusinessAssignedFeaturesRepository $asgnFeatureRepo
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
     * Get business package by id
     * @return Response
     */
    public function getServiceById($serviceId) {
        $service = $this->otherRepo->getServiceById($serviceId);

        $data['packageDetails'] = $service;
        $data['components'] = $this->_getComponents($serviceId);
        $data['feature'] = $this->_getFeaturesByService($service['type'], $serviceId);
        return $this->responseFormatter->sendSuccessResponse($data, 'Enterprise Solutions Details');
    }

    /**
     * Get components by service ID
     * @return Response
     */
    private function _getComponents($serviceId) {

        $components = [];
        $photoText = $this->photoTextRepo->getComponent($serviceId);
        foreach ($photoText as $v) {
            $position = $v->position;
            $components[$position]['type'] = 'photo-with-text';
            $components[$position]['data']['text_en'] = $v->text;
            $components[$position]['data']['text_bn'] = $v->text_bn;
            $components[$position]['data']['photo_url'] = config('filesystems.image_host_url') . $v->photo_url;
            $components[$position]['data']['alt_text'] = $v->alt_text;
        }

        $packageOne = $this->pkOneRepo->getComponent($serviceId);
//        return $packageOne;

        $prePos = 0;
        $pk1Count = 0;
        foreach ($packageOne as $k => $v) {
            $position = $v->position;

            if ($prePos != $position) {
                $pk1Count = 0;
            }
            $prePos = $position;

            $components[$position]['type'] = 'package-comparison-one';
            $components[$position]['data'][$pk1Count]['table_head_en'] = $v->table_head;
            $components[$position]['data'][$pk1Count]['table_head_bn'] = $v->table_head_bn;
            $components[$position]['data'][$pk1Count]['feature_text_en'] = $v->feature_text_en;
            $components[$position]['data'][$pk1Count]['feature_text_bn'] = $v->feature_text_bn;
            $components[$position]['data'][$pk1Count]['price_en'] = $v->price;
            $components[$position]['data'][$pk1Count]['price_bn'] = $v->price_bn;
            $pk1Count++;
        }

        $packageTwo = $this->pkTwoRepo->getComponent($serviceId);
        $prePos = 0;
        $pk2Count = 0;
        foreach ($packageTwo as $k => $v) {
            $position = $v->position;

            if ($prePos != $position) {
                $pk2Count = 0;
            }
            $prePos = $position;

            $components[$position]['type'] = 'package-comparison-two';
            $components[$position]['data'][$pk2Count]['title_en'] = $v->title;
            $components[$position]['data'][$pk2Count]['title_bn'] = $v->title_bn;
            $components[$position]['data'][$pk2Count]['package_name_en'] = $v->package_name;
            $components[$position]['data'][$pk2Count]['package_name_bn'] = $v->package_name_bn;
            $components[$position]['data'][$pk2Count]['data_limit_en'] = $v->data_limit;
            $components[$position]['data'][$pk2Count]['data_limit_bn'] = $v->data_limit_bn;
            $components[$position]['data'][$pk2Count]['package_days_en'] = $v->package_days;
            $components[$position]['data'][$pk2Count]['package_days_bn'] = $v->package_days_bn;
            $components[$position]['data'][$pk2Count]['price_en'] = $v->price;
            $components[$position]['data'][$pk2Count]['price_bn'] = $v->price_bn;
            $pk2Count++;
        }


        $features = $this->featureRepo->getComponent($serviceId);

        foreach ($features as $v) {
            $position = $v->position;
            $components[$position]['type'] = 'product-features';
            $components[$position]['data']['feature_text_en'] = $v->feature_text;
            $components[$position]['data']['feature_text_bn'] = $v->feature_text_bn;
            $components[$position]['data']['photo_url'] = "";
        }


        $priceTable = $this->priceTableRepo->getComponent($serviceId);

        foreach ($priceTable as $v) {
            $position = $v->position;


            $headEnArray = json_decode($v->table_head);
            $headBnArray = json_decode($v->table_head_bn);

            $bodyEnArray = json_decode($v->table_body);
            $bodyEn = [];

            if (!empty($bodyEnArray)) {
                $rowsEn = count($bodyEnArray[0]);
                for ($i = 0; $i < $rowsEn; $i++) {
                    $count = 0;
                    foreach ($bodyEnArray as $k => $val) {
                        $bodyEn[$i][$count] = $val[$i];
                        $count++;
                    }
                }
            }

            $bodyBnArray = json_decode($v->table_body_bn);
            $bodyBn = [];

            if (!empty($bodyBnArray)) {
                $rowsBn = count($bodyBnArray[0]);
                for ($i = 0; $i < $rowsBn; $i++) {
                    $count = 0;
                    foreach ($bodyBnArray as $k => $val) {
                        $bodyBn[$i][$count] = $val[$i];
                        $count++;
                    }
                }
            }

            $components[$position]['type'] = 'product-price-table';
            $components[$position]['data']['title_en'] = $v->title;
            $components[$position]['data']['title_bn'] = $v->title_bn;
            $components[$position]['data']['table_head_en'] = $headEnArray;
            $components[$position]['data']['table_head_bn'] = $headBnArray;
            $components[$position]['data']['table_body_en'] = $bodyEn;
            $components[$position]['data']['table_body_bn'] = $bodyBn;
        }


        $video = $this->videoRepo->getComponent($serviceId);
        foreach ($video as $v) {
            $position = $v->position;
            $components[$position]['type'] = 'video-component';
            $components[$position]['name_en'] = $v->name;
            $components[$position]['name_bn'] = $v->name_bn;
            $components[$position]['title_en'] = $v->title;
            $components[$position]['title_bn'] = $v->title_bn;
            $components[$position]['description_en'] = $v->description;
            $components[$position]['description_bn'] = $v->description_bn;
            $components[$position]['embed_code'] = $v->embed_code;
        }



        $photos = $this->photoRepo->getComponent($serviceId);

        foreach ($photos as $v) {
            $position = $v->position;
            $components[$position]['type'] = 'photo-component';
            $components[$position]['photo_one'] = config('filesystems.image_host_url') . $v->photo_one;
            $components[$position]['alt_text_one'] = $v->alt_text_one;
            $components[$position]['photo_two'] = config('filesystems.image_host_url') . $v->photo_two;
            $components[$position]['alt_text_two'] = $v->alt_text_two;
            $components[$position]['photo_three'] = config('filesystems.image_host_url') . $v->photo_three;
            $components[$position]['alt_text_three'] = $v->alt_text_three;
            $components[$position]['photo_four'] = config('filesystems.image_host_url') . $v->photo_four;
            $components[$position]['alt_text_four'] = $v->alt_text_four;
        }

        ksort($components);
        $comCount = 0;
        $data = [];
        foreach ($components as $val) {
            $data[$comCount] = $val;
            $comCount++;
        }
        return $data;
    }

    /**
     * Get business package by id
     * @return Response
     */
    private function _getFeaturesByService($serviceType, $serviceId) {
        $types = array("business-solution" => 2, "iot" => 3, "others" => 4);
        $parentType = $types[$serviceType];
        $response = $this->asgnFeatureRepo->getAssignedFeatures($serviceId, $parentType);
        return $response;
    }

}
