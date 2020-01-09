<?php
namespace App\Repositories;

use App\Models\Product;
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
        return $this->model->where('status', 1)
            ->startEndDate()
            ->productCore()
            ->category($type)
            ->get();
    }

    public function showTrendingProduct()
    {
       return $this->model->where('show_in_home', 1)
            ->productCore()
            ->where('status', 1)
            ->startEndDate()
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
            ->category($type)
            ->with('product_details', 'related_product', 'other_related_product')
            ->first();
    }

    public function rechargeOffers()
    {
        return $this->model->select('*')->whereIn('purchase_option', ['all', 'recharge'])
            ->where('status', 1)
            ->whereHas('productCore', function($query){
                $query->whereNotNull('recharge_product_code')
                ->whereIn('platform', ['all', 'web']);
            })
            // ->productCore()
            ->get();
    }

    public function rechargeOfferByAmount($amount)
    {
        //TODO:add filter by start and end date
        //
        # check price range
        $check_product_code = ProductPriceSlab::where('range_start', '<=', (int)$amount)->where('range_end', '>=', (int)$amount)->first();

        $check_product_code = !empty($check_product_code->product_code) ? $check_product_code->product_code : null;
        

        return $this->model->join('product_cores', 'products.product_code', 'product_cores.product_code')
            ->selectRaw('products.*, product_cores.activation_ussd as ussd_en, product_cores.balance_check_ussd, product_cores.mrp_price as price_tk,
             product_cores.validity as validity_days,product_cores.validity_unit, product_cores.internet_volume_mb,product_cores.sms_volume,product_cores.minute_volume,product_cores.call_rate,product_cores.sms_rate')
            ->whereIn('products.purchase_option', ['recharge'])
            ->where('products.status', 1)
            ->whereIn('product_cores.platform', ['all', 'web'])
            // ->where('products.sim_category_id', 1) // Check prepaid sim type
            ->whereHas('sim_category', function ($query) {
                $query->where('alias', 'prepaid');
            })
            // ->where('product_cores.mrp_price', '=', $amount)
            ->where(function($query) use ($amount, $check_product_code){
                if( !empty($check_product_code) ){
                    return $query->where('product_cores.mrp_price', '=', $amount)->orWhere('product_cores.recharge_product_code', '=', $check_product_code);
                }
                else{
                    return $query->where('product_cores.mrp_price', '=', $amount);
                }
                

            })
            ->orderBy('product_cores.mrp_price')
            ->first();
    }

    public function relatedProducts($id)
    {
        return $this->model->where('id', $id)
            ->productCore()
            ->first();
    }

    public function bookmarkProduct($productCode)
    {
        return $this->model->where('product_code', $productCode)
            ->productCore()
            ->first();
    }

    public function bondhoSimOffer()
    {
        return $this->model->where('offer_info->other_offer_type_id',  13)
            ->productCore()
            ->get();
    }

}
