<?php

namespace App\Repositories;

use App\Models\DeviceOffer;
use App\Models\EasyPaymentCard;

class DeviceOfferRepository extends BaseRepository {

    public $modelName = DeviceOffer::class;

    /**
     * @param $brand
     * @return mixed
     */
    public function getList($brand, $model) {
        $offers = $this->model->where('status', 1)
                ->select('brand', 'model', 'free_data_one', 'free_data_two', 'free_data_three', 'bonus_data_one', 'bonus_data_two', 'bonus_data_three', 'available_shop');

        if (!empty($brand)) {
            $offers->where('brand', $brand);
            if (!empty($model)){
                $offers->where('model', $model);
            }
        }

        $offerList = ($brand && empty($model)) ? $offers->get() : $offers->first();

        $brandList = $this->model->select('brand')->groupBy('brand')->get();
        $brands = [];
        foreach ($brandList as $v) {
            $brands[] = $v['brand'];
        }

        $codes = [];
        if (isset($offerList['available_shop'])){
            $codes = explode(',', $offerList['available_shop']);
        }
        $locationData = [];
        foreach ($codes as $key => $code){
            $data = EasyPaymentCard::where('code', str_replace(' ', '', $code))->first();
            if(!empty($data)){
               array_push($locationData, $data);
            }
        }
        return array('brands' => $brands, 'offers' => ($brand) ? $offerList : null, 'shopLocation' => $locationData);
    }

}
