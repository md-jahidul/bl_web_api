<?php
namespace App\Repositories;

use App\Models\AlCoreProduct;
use App\Models\Product;
use App\Models\ProductCore;
use App\Models\ProductPriceSlab;
use Carbon\Carbon;

class ProductRepository extends BaseRepository
{
    /**
     * @var string
     */
    public $modelName = Product::class;

    /**
     * @param $type
     * @return mixed
     */
    public function simTypeProduct($type)
    {
        return $this->model->select(
                'products.id',
                'products.product_code',
                'products.url_slug',
                'products.schema_markup',
                'products.page_header',
                'products.rate_cutter_unit',
                'products.rate_cutter_offer',
                'products.name_en',
                'products.name_bn',
                'products.ussd_bn',
                'products.balance_check_ussd_bn',
                'products.call_rate_unit_bn',
                'products.sms_rate_unit_bn',
                'products.tag_category_id',
                'products.sim_category_id',
                'products.offer_category_id',
                'products.special_product',
                'products.like',
                'products.validity_postpaid',
                'products.offer_info'
//                'd.url_slug'
            )
//            ->leftJoin('product_details as d', 'd.product_id', '=', 'products.id')
            ->where('status', 1)
            ->where('special_product', 0)
            ->startEndDate()
            ->productCore()
            ->category($type)
            ->get();
    }

    public function showTrendingProduct()
    {
       return $this->model->select(
               'id',
               'product_code',
               'url_slug',
               'schema_markup',
               'page_header',
               'rate_cutter_unit',
               'rate_cutter_offer',
               'name_en',
               'name_bn',
               'ussd_bn',
               'balance_check_ussd_bn',
               'call_rate_unit_bn',
               'sms_rate_unit_bn',
               'tag_category_id',
               'sim_category_id',
               'offer_category_id',
               'special_product',
               'like',
               'validity_postpaid',
               'offer_info'
           )
           ->productCore()
           ->startEndDate()
           ->where('status', 1)
           ->where('show_in_home', 1)
           ->where('special_product', 0)
           ->orderBy('display_order')
           ->get();
    }

    /**
     * @param $type
     * @param $id
     * @return mixed
     */
    public function detailProducts($type, $id)
    {
        return $data = $this->model->where('id', $id)
            ->productCore()
            ->select(
                'id',
                'product_code',
                'rate_cutter_offer',
                'url_slug',
                'schema_markup',
                'page_header',
                'sim_category_id',
                'offer_category_id',
                'offer_info',
                'validity_postpaid',
                'name_en',
                'name_bn',
                'ussd_bn',
                'call_rate_unit_bn',
                'offer_info',
                'status',
                'like')
            ->category($type)
            ->with('product_details', 'related_product', 'other_related_product')
            ->first();
    }

    public function rechargeOffers()
    {
         return $this->model->where('purchase_option', 'recharge')
                 ->where('status', 1)
                 ->where('special_product', 0)
                 ->productCore()
                 ->get();
    }

    public function rechargeOfferByAmount($amount)
    {
        //TODO:add filter by start and end date
        //
        # check price range
        $check_product_code = ProductPriceSlab::where('range_start', '<=', (int)$amount)->where('range_end', '>=', (int)$amount)->first();

        $check_product_code = !empty($check_product_code->product_code) ? $check_product_code->product_code : null;

        return $this->model->join('al_core_products', 'products.product_code', 'al_core_products.product_code')
            ->selectRaw('products.*, al_core_products.activation_ussd as ussd_en, al_core_products.balance_check_ussd, al_core_products.mrp_price as price_tk,
             al_core_products.validity as validity_days,al_core_products.validity_unit, al_core_products.internet_volume_mb,al_core_products.sms_volume,al_core_products.minute_volume,al_core_products.call_rate as callrate_offer,al_core_products.sms_rate as sms_rate_offer')
//            ->whereIn('products.purchase_option', ['recharge'])
            ->where('products.status', 1)
//            ->whereIn('al_core_products.platform', ['all', 'web'])
            // ->where('products.sim_category_id', 1) // Check prepaid sim type
            ->whereHas('sim_category', function ($query) {
                $query->where('alias', 'prepaid');
            })
            // ->where('al_core_products.mrp_price', '=', $amount)
            ->where(function ($query) use ($amount, $check_product_code) {
                if (!empty($check_product_code)) {
                    return $query->where('al_core_products.mrp_price', $amount)->orWhere('al_core_products.recharge_product_code', $check_product_code);
                } else {
                    return $query->where('al_core_products.mrp_price', '=', $amount);
                }

            })
            ->orderBy('al_core_products.mrp_price')
            ->first();
    }

    public function relatedProducts($id)
    {
        return $this->model->where('id', $id)
            ->select(
                'id',
                'product_code',
                'rate_cutter_offer',
                'product_code',
                'url_slug',
                'schema_markup',
                'page_header',
                'name_en',
                'name_bn',
                'ussd_bn',
                'call_rate_unit_bn',
                'balance_check_ussd_bn',
                'tag_category_id',
                'sim_category_id',
                'offer_category_id',
                'like')
            ->productCore()
            ->first();
    }

    public function bookmarkProduct($productCode)
    {
        return $this->model->where('product_code', $productCode)
            ->productCore()
            ->first();
    }

    public function rechargeBenefitsOffer($productCode)
    {
        if ($productCode) {
            return AlCoreProduct::where('recharge_product_code', $productCode)
                ->select(
                    'product_code',
                    'activation_ussd as ussd_en',
                    'balance_check_ussd',
                    'price',
                    'vat',
                    'mrp_price as price_tk',
                    'validity as validity_days',
                    'validity_unit',
                    'internet_volume_mb',
                    'sms_volume',
                    'minute_volume',
                    'call_rate as callrate_offer',
                    'sms_rate as sms_rate_offer',
                    'renew_product_code',
                    'recharge_product_code'
                )
                ->first();
        }
    }

    public function bondhoSimOffer()
    {
        return $this->model->where('offer_info->other_offer_type_id',  13)
            ->productCore()
            ->get();
    }

    public function findOneProduct($type, $id)
    {
        return $data = $this->model->where('id', $id)
            ->productCore()
            ->select(
                'id',
                'product_code',
                'url_slug',
                'schema_markup',
                'page_header',
                'tag_category_id',
                'sim_category_id',
                'offer_category_id',
                'offer_info',
                'name_en',
                'name_bn',
                'ussd_bn',
                'offer_info',
                'status',
                'like')
            ->category($type)
            ->first();
    }

}
