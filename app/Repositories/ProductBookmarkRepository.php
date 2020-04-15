<?php

namespace App\Repositories;

use App\Models\AlProductBookmark;

class ProductBookmarkRepository extends BaseRepository {

    /**
     * @var string
     */
    public $modelName = AlProductBookmark::class;

    public function getAppAndService($mobile) {
        $response = $this->model->select("ast.name_en as tab_en", "ast.name_bn as tab_bn", "ast.alias", "as.*")
                        ->leftJoin('app_service_products as as', 'as.id', '=', 'al_product_bookmarks.product_id')
                        ->leftJoin('app_service_tabs as ast', 'ast.id', '=', 'as.app_service_tab_id')
                        ->where(['al_product_bookmarks.module_type' => 'app-and-service', 'al_product_bookmarks.mobile' => $mobile])
                        ->orderBy("ast.alias")->get();

        return $response;
    }

    public function getBusiness($mobile) {
        $response = $this->model->select("c.name as cat_en", "c.name_bn as cat_bn", "bi.*")
                ->leftJoin('business_internet_packages as bi', 'bi.id', '=', 'al_product_bookmarks.product_id')
                ->leftJoin('business_product_categories as c', 'c.id', '=', 'c.id')
                ->where(['al_product_bookmarks.module_type' => 'business', 'al_product_bookmarks.mobile' => $mobile, 'c.id' => 2])
                ->get();

        return $response;
    }

    public function getOffers($mobile) {
        $response['products'] = $this->model->selectRaw(
                        "al_product_bookmarks.category as bookmark_category, concat(s.name, ' ', c.name_en) as cat_en,"
                        . " concat(s.name, ' ', c.name_bn) as cat_bn,"
                        . "s.alias as sim_alias,"
                        . "c.alias as cat_alias,"
                        . " pd.url_slug, p.*, pc.balance_check_ussd, pc.mrp_price as price_tk, pc.price, pc.vat, pc.activation_ussd as ussd_en, "
                        . " pc.validity, pc.validity_unit, pc.validity_in_days as validity_days, pc.internet_volume_mb, pc.call_rate as callrate_offer, pc.call_rate_unit, pc.sms_volume, pc.minute_volume"
                )
                ->leftJoin('products as p', 'p.id', '=', 'al_product_bookmarks.product_id')
                ->leftJoin('product_cores as pc', 'pc.product_code', '=', 'p.product_code')
                ->leftJoin('product_details as pd', 'pd.product_id', '=', 'p.id')
                ->leftJoin('sim_categories as s', 's.id', '=', 'p.sim_category_id')
                ->leftJoin('offer_categories as c', 'c.id', '=', 'p.offer_category_id')
                ->where('al_product_bookmarks.module_type', '=', 'offers')
                ->where('al_product_bookmarks.mobile', '=', $mobile)
                ->whereRaw("(al_product_bookmarks.category = 'prepaid-internet' OR al_product_bookmarks.category = 'prepaid-voice'"
                        . " OR al_product_bookmarks.category = 'prepaid-bundle' OR al_product_bookmarks.category = 'postpaid-internet'"
                        . " OR al_product_bookmarks.category = 'prepaid-other-offers')")->orderBy('al_product_bookmarks.category')
                ->get();

        $response['roming_bundle_offers'] = $this->model->selectRaw(
                        "al_product_bookmarks.category as bookmark_category, b.*"
                )
                ->leftJoin('roaming_bundle_offers as b', 'b.id', '=', 'al_product_bookmarks.product_id')
                ->where('al_product_bookmarks.module_type', '=', 'offers')
                ->where('al_product_bookmarks.mobile', '=', $mobile)
                ->whereRaw("(al_product_bookmarks.category = 'roaming-bundle-offer')")
                ->get();

        $response['roaming_others_offers'] = $this->model->selectRaw(
                        "al_product_bookmarks.category as bookmark_category, o.*"
                )
                ->leftJoin('roaming_other_offer as o', 'o.id', '=', 'al_product_bookmarks.product_id')
                ->where('al_product_bookmarks.module_type', '=', 'offers')
                ->where('al_product_bookmarks.mobile', '=', $mobile)
                ->whereRaw("(al_product_bookmarks.category = 'roaming-others-offer')")
                ->get();

        $response['roaming_info_tips'] = $this->model->selectRaw(
                        "al_product_bookmarks.category as bookmark_category, i.*"
                )
                ->leftJoin('roaming_info_tips as i', 'i.id', '=', 'al_product_bookmarks.product_id')
                ->where('al_product_bookmarks.module_type', '=', 'offers')
                ->where('al_product_bookmarks.mobile', '=', $mobile)
                ->whereRaw("(al_product_bookmarks.category = 'roaming-info-tips')")
                ->get();

        return $response;
    }

}
