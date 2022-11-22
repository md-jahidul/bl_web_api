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
            $data[$k]['offers'] = [];
            foreach ($offers as $key => $v) {
                $data[$k]['offers'][$key]['id'] = $v->id;
                $data[$k]['offers'][$key]['name_en'] = $v->name_en;
                $data[$k]['offers'][$key]['name_bn'] = $v->name_bn;
                $data[$k]['offers'][$key]['url_slug'] = $v->url_slug;
                $data[$k]['offers'][$key]['url_slug_bn'] = $v->url_slug_bn;
                $data[$k]['offers'][$key]['page_header'] = $v->page_header;
                $data[$k]['offers'][$key]['page_header_bn'] = $v->page_header_bn;
                $data[$k]['offers'][$key]['schema_markup'] = $v->schema_markup;
                $data[$k]['offers'][$key]['card_text_en'] = $v->card_text_en;
                $data[$k]['offers'][$key]['card_text_bn'] = $v->card_text_bn;
                $data[$k]['offers'][$key]['card_image'] = $v->card_image;
                $data[$k]['offers'][$key]['likes'] = $v->likes;
            }
        }




        return $data;
    }

    public function getOtherOffersDetails($offerSlug)
    {
        $offer = $this->model->where('url_slug', $offerSlug)->orWhere('url_slug_bn', $offerSlug)->first();

        $data = [];

        $data['name_en'] = $offer->name_en;
        $data['name_bn'] = $offer->name_bn;
        $data['short_text_en'] = $offer->short_text_en;
        $data['short_text_bn'] = $offer->short_text_bn;
        $data['banner_web'] = $offer->banner_web == "" ? "" : config('filesystems.image_host_url') . $offer->banner_web;
        $data['banner_mobile'] = $offer->banner_mobile == "" ? "" : config('filesystems.image_host_url') . $offer->banner_mobile;
        $data['alt_text'] = $offer->alt_text;
        $data['url_slug'] = $offer->url_slug;
        $data['url_slug_bn'] = $offer->url_slug_bn;
        $data['page_header'] = $offer->page_header;
        $data['page_header_bn'] = $offer->page_header_bn;
        $data['schema_markup'] = $offer->schema_markup;
        $data['likes'] = $offer->likes;

        $components = RoamingOtherOfferComponents::where('parent_id', $offer->id)->orderBy('position')->get();
        $data['components'] = [];
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

    public function ratesAndBundle($country, $operator) {
        $ratesObj = RoamingRates::where(array('country' => $country, 'operator' => $operator));

        if ($ratesObj->count() == 0) {
            $ratesObj = RoamingRates::where(array('country' => $country));
        }

        $rates = $ratesObj->get();

        $data = [];

        $data['rates'] = array();
        foreach ($rates as $k => $val) {
            $data['rates'][$k]['id'] = $val->id;
            $data['rates'][$k]['subscription_type'] = $val->subscription_type;
            $data['rates'][$k]['call_rate'] = $val->rate_visiting_country;
            $data['rates'][$k]['call_rate_bangladesh'] = $val->rate_bangladesh;
            $data['rates'][$k]['sms_rate'] = $val->sms_rate;
            $data['rates'][$k]['data_rate'] = $val->gprs;
        }

        $bundles = RoamingBundles::where(array('country' => $country, 'operator' => $operator, 'status' => 1))->get();

        $data['prepaid_bundles'] = array();
        $data['postpaid_bundles'] = array();

        $preCount = 0;
        $postCount = 0;
        foreach ($bundles as $key => $val) {

            if (strtolower($val->subscription_type) != 'postpaid') {
                $data['prepaid_bundles'][$preCount]['id'] = $val->id;
                $data['prepaid_bundles'][$preCount]['subscription_type'] = $val->subscription_type;
                $data['prepaid_bundles'][$preCount]['product_code'] = $val->product_code;
                $data['prepaid_bundles'][$preCount]['package_name_en'] = $val->package_name_en;
                $data['prepaid_bundles'][$preCount]['package_name_bn'] = $val->package_name_bn;
                $data['prepaid_bundles'][$preCount]['data_volume'] = $val->data_volume;
                $data['prepaid_bundles'][$preCount]['data_volume_unit'] = $val->volume_data_unit;
                $data['prepaid_bundles'][$preCount]['validity'] = $val->validity;
                $data['prepaid_bundles'][$preCount]['validity_unit'] = $val->validity_unit;
                $data['prepaid_bundles'][$preCount]['sms_volume'] = $val->sms_volume;
                $data['prepaid_bundles'][$preCount]['minute_volume'] = $val->minute_volume;
                $data['prepaid_bundles'][$preCount]['price_tk'] = round($val->mrp, 2);
                $data['prepaid_bundles'][$preCount]['like'] = $val->like;
                $data['prepaid_bundles'][$preCount]['details_en'] = $val->details_en;
                $data['prepaid_bundles'][$preCount]['details_bn'] = $val->details_bn;
                $preCount++;
            }

            if (strtolower($val->subscription_type) != 'prepaid') {
                $data['postpaid_bundles'][$postCount]['id'] = $val->id;
                $data['postpaid_bundles'][$postCount]['subscription_type'] = $val->subscription_type;
                $data['postpaid_bundles'][$postCount]['product_code'] = $val->product_code;
                $data['postpaid_bundles'][$postCount]['package_name_en'] = $val->package_name_en;
                $data['postpaid_bundles'][$postCount]['package_name_bn'] = $val->package_name_bn;
                $data['postpaid_bundles'][$postCount]['data_volume'] = $val->data_volume;
                $data['postpaid_bundles'][$postCount]['data_volume_unit'] = $val->volume_data_unit;
                $data['postpaid_bundles'][$postCount]['validity'] = $val->validity;
                $data['postpaid_bundles'][$postCount]['validity_unit'] = $val->validity_unit;
                $data['postpaid_bundles'][$postCount]['sms_volume'] = $val->sms_volume;
                $data['postpaid_bundles'][$postCount]['minute_volume'] = $val->minute_volume;
                $data['postpaid_bundles'][$postCount]['price_tk'] = round($val->mrp, 2);
                $data['postpaid_bundles'][$postCount]['like'] = $val->like;
                $data['postpaid_bundles'][$postCount]['details_en'] = $val->details_en;
                $data['postpaid_bundles'][$postCount]['details_bn'] = $val->details_bn;
            }
        }

        return $data;
    }

    public function bundleLike($bundleId) {

        $bundle = RoamingBundles::findOrFail($bundleId);
        $likes = $bundle->like + 1;
        $bundle->like = $likes;
        $bundle->save();
        $data['likes'] = $likes;
        return $data;
    }

    public function otherOfferLike($offerId) {

        $offer = $this->model->findOrFail($offerId);
        $likes = $offer->likes + 1;
        $offer->likes = $likes;
        $offer->save();
        $data['likes'] = $likes;
        return $data;
    }

    public function roamingRates() {
        $rates = RoamingRates::orderBy('region')->orderBy('country')->orderBy('operator')->get();

        $region = [];
        foreach ($rates as $k => $val) {
            $region[$val->region][$k] = $val;
        }

        $data = [];
        $rCount = 0;
        foreach ($region as $k => $reg) {
            $rateCount = 0;

            $data[$rCount]['region'] = $k;
            foreach ($reg as $val) {
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
