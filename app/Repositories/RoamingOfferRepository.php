<?php

/**
 * User: Bulbul Mahmud Nito
 * Date: 26/02/2020
 */

namespace App\Repositories;

use App\Models\RoamingOtherOfferCategory;
use App\Models\RoamingOtherOffer;
use App\Models\RoamingOtherOfferComponents;
use App\Models\RoamingRates;
use App\Models\RoamingBundles;

class RoamingOfferRepository extends BaseRepository {

    public $modelName = RoamingOtherOffer::class;

    public function getOtherOffers() {

        $offerCategory = RoamingOtherOfferCategory::where('status', 1)->get();


        $data = [];

        $catCount = 0;
        foreach ($offerCategory as $k => $c) {
            $data[$k]['category_en'] = $c->name_en;
            $data[$k]['category_bn'] = $c->name_bn;
            
            $offers = $this->model->where('status', 1)->where('category_id', $c->id)->orderBy('id', 'desc')->get();
            foreach ($offers as $key => $v) {
                $data[$k]['offers'][$key]['id'] = $v->id;
                $data[$k]['offers'][$key]['name_en'] = $v->name_en;
                $data[$k]['offers'][$key]['name_bn'] = $v->name_bn;
                $data[$k]['offers'][$key]['url_slug'] = $v->url_slug;
                $data[$k]['offers'][$key]['card_text_en'] = $v->card_text_en;
                $data[$k]['offers'][$key]['card_text_bn'] = $v->card_text_bn;
                $data[$k]['offers'][$key]['likes'] = $v->likes;
            }
        }




        return $data;
    }
    
    public function getOtherOffersDetails($offerId) {

        $offer = $this->model->findOrFail($offerId);


        $data = [];
        
        $data['name_en'] = $offer->name_en;
        $data['name_bn'] = $offer->name_bn;
        $data['short_text_en'] = $offer->short_text_en;
        $data['short_text_bn'] = $offer->short_text_bn;
        $data['banner_web'] = config('filesystems.image_host_url') . $offer->banner_web;
        $data['banner_mobile'] = config('filesystems.image_host_url') . $offer->banner_mobile;
        $data['alt_text'] = $offer->alt_text;
        $data['page_header'] = $offer->page_header;
        $data['schema_markup'] = $offer->schema_markup;
        $data['likes'] = $offer->likes;

        $components = RoamingOtherOfferComponents::where('parent_id', $offerId)->orderBy('position')->get();
        foreach ($components as $k => $val) {
            
            $textEn = json_decode($val->body_text_en);
            $textBn = json_decode($val->body_text_bn);
            
            $data['components'][$k]['component_type'] = $val->component_type;
            $data['components'][$k]['data_en'] = $textEn;
            $data['components'][$k]['data_bn'] = $textBn;
         
        }
        
         $data['details_en'] = $offer->details_en;
         $data['details_bn'] = $offer->details_en;




        return $data;
    }
    
    
    
    public function ratesAndBundle($country, $operator){
        $rates = RoamingRates::where(array('country' => $country, 'operator' => $operator))->get();
        
        $data = [];
        foreach($rates as $k => $val){
            $data['rates'][$k]['id'] = $val->id;
            $data['rates'][$k]['subscription_type'] = $val->subscription_type;
            $data['rates'][$k]['call_rate'] = $val->rate_visiting_country;
            $data['rates'][$k]['call_rate_bangladesh'] = $val->rate_bangladesh;
            $data['rates'][$k]['sms_rate'] = $val->sms_rate;
            $data['rates'][$k]['data_rate'] = $val->gprs;
        }
        
        $bundles = RoamingBundles::where(array('country' => $country, 'operator' => $operator, 'status' => 1))->get();
        
          foreach($bundles as $key => $val){
            $data['bundles'][$key]['id'] = $val->id;
            $data['bundles'][$key]['subscription_type'] = $val->subscription_type;
            $data['bundles'][$key]['product_code'] = $val->product_code;
            $data['bundles'][$key]['package_name_en'] = $val->package_name_en;
            $data['bundles'][$key]['package_name_bn'] = $val->package_name_bn;
            $data['bundles'][$key]['data_volume'] = $val->data_volume;
            $data['bundles'][$key]['data_volume_unit'] = $val->volume_data_unit;
            $data['bundles'][$key]['validity'] = $val->validity;
            $data['bundles'][$key]['validity_unit'] = $val->validity_unit;
            $data['bundles'][$key]['mrp'] = $val->mrp;
            $data['bundles'][$key]['price'] = $val->price;
            $data['bundles'][$key]['tax'] = $val->tax;
            $data['bundles'][$key]['like'] = $val->like;
            $data['bundles'][$key]['details_en'] = $val->details_en;
            $data['bundles'][$key]['details_bn'] = $val->details_bn;
        }
        
        return $data;
    }
    
    public function roamingRates(){
        $rates = RoamingRates::orderBy('region')->orderBy('country')->orderBy('operator')->get();
        
        $region = [];
        foreach($rates as $k => $val){
            $region[$val->region][$k] = $val;
        }
        
        $data = [];
        $rCount = 0;
        foreach($region as $k => $reg){
            $rateCount = 0;
            
            $data[$rCount]['region'] = $k;
            foreach($reg as $val){
                $data[$rCount]['rates'][$rateCount]['subscription_type'] = $val->subscription_type;
                $data[$rCount]['rates'][$rateCount]['country'] = $val->country;
                $data[$rCount]['rates'][$rateCount]['operator'] = $val->operator;
                $data[$rCount]['rates'][$rateCount]['voice_visiting_country'] = $val->rate_visiting_country;
                $data[$rCount]['rates'][$rateCount]['voice_bangladesh'] = $val->rate_bangladesh;
                $data[$rCount]['rates'][$rateCount]['sms_rate'] = $val->sms_rate;
                $data[$rCount]['rates'][$rateCount]['data_rate'] = $val->gprs;
                $rateCount++;
            }
            $rCount++;
        }
        
        return $data;
    }
    
   

}
