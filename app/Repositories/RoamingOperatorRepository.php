<?php

/**
 * User: Bulbul Mahmud Nito
 * Date: 24/02/2020
 */

namespace App\Repositories;

use App\Models\RoamingOperator;

class RoamingOperatorRepository extends BaseRepository {

    public $modelName = RoamingOperator::class;

    public function getCountries() {
        $country = $this->model
                        ->GroupBy('country_en')
                        ->orderBy('country_en')->get();
        $data = [];
        $count = 0;


        foreach ($country as $v) {
            $data[$count]['id'] = $v->id;
            $data[$count]['country_en'] = $v->country_en;
            $data[$count]['country_bn'] = $v->country_bn;
            $count++;
        }
        return $data;
    }
    
    public function getSingleOperator($operator){
       $data = $this->model->select('details_en', 'details_bn')
                        ->where('operator_en', $operator)->first(); 
       
       if(empty($data)){
           return array();
       }
       return $data;
    }

    public function getOperators($countryName) {
        $operators = $this->model
                        ->where('country_en', $countryName)
                        ->orderBy('operator_en')->get();
        $data = [];
        $count = 0;


        foreach ($operators as $v) {
            $data[$count]['id'] = $v->id;
            $data[$count]['operator_en'] = $v->operator_en;
            $data[$count]['operator_bn'] = $v->operator_bn;           
            $count++;
        }
        return $data;
    }

}
