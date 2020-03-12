<?php


/**
 * Dev: Bulbul Mahmud Nito
 * Date: 10/03/2020
 */

namespace App\Repositories;

use App\Models\SearchData;
use App\Models\SearchSettings;

class SearchRepository extends BaseRepository {

     public $modelName = SearchData::class;
     
     public function searchSuggestion($keyword) {
         $limits = $this->getSettingData();
         
         $preIntLimit = $limits['prepaid-internet'];
         $preInternet = $this->model->select('product_name as keyword', 'url as product_url', 'type')
                 ->where('status', 1)->where('type', 'prepaid-internet')->where('product_name', 'like', '%'.$keyword.'%')
                 ->orWhere('tag', 'like', '%'.$keyword.'%')->offset(0)->limit($preIntLimit)->get();
         
         $preVoiceLimit = $limits['prepaid-voice'];
         $preVoice = $this->model->select('product_name as keyword', 'url as product_url', 'type')
                 ->where('status', 1)->where('type', 'prepaid-voice')->where('product_name', 'like', '%'.$keyword.'%')
                 ->orWhere('tag', 'like', '%'.$keyword.'%')->offset(0)->limit($preVoiceLimit)->get();
         
         $preBundleLimit = $limits['prepaid-bundle'];
         $preBundle = $this->model->select('product_name as keyword', 'url as product_url', 'type')
                 ->where('status', 1)->where('type', 'prepaid-bundle')->where('product_name', 'like', '%'.$keyword.'%')
                 ->orWhere('tag', 'like', '%'.$keyword.'%')->offset(0)->limit($preBundleLimit)->get();
         
         $postIntLimit = $limits['postpaid-internet'];
         $postInt = $this->model->select('product_name as keyword', 'url as product_url', 'type')
                 ->where('status', 1)->where('type', 'postpaid-internet')->where('product_name', 'like', '%'.$keyword.'%')
                 ->orWhere('tag', 'like', '%'.$keyword.'%')->offset(0)->limit($postIntLimit)->get();
         
         $otherLimit = $limits['others'];
         $others = $this->model->select('product_name as keyword', 'url as product_url', 'type')
                 ->where('status', 1)->where('type', 'others')->where('product_name', 'like', '%'.$keyword.'%')
                 ->orWhere('tag', 'like', '%'.$keyword.'%')->offset(0)->limit($otherLimit)->get();
         
         $resopnse = array($preInternet ,$preVoice, $preBundle, $postInt, $others);
         return $resopnse;
    }
     public function searchData($keyword) {
         $response = $this->model->select('product_name as keyword', 'url as product_url', 'type')
                 ->where('status', 1)->where('product_name', 'like', '%'.$keyword.'%')
                 ->orWhere('tag', 'like', '%'.$keyword.'%')->get();
        return $response;
    }
    
    public function getSettingData(){
        $settings = SearchSettings::select('type_slug', 'limit')->get();
        
        $data = [];
        foreach($settings as $val){
            $data[$val->type_slug] = $val->limit;
        }
        return $data;
    }
   
}
