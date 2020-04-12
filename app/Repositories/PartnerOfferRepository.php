<?php
namespace App\Repositories;

use App\Models\PartnerOffer;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class PartnerOfferRepository extends BaseRepository
{
    /**
     * @var string
     */
    public $modelName = PartnerOffer::class;

    /**
     * @param $type
     * @param $id
     * @return mixed
     */
    public function detailProducts($type, $id)
    {
        return $this->model->where('id', $id)
            ->category($type)
            ->with('product_details', 'related_product', 'other_related_product')
            ->first();
    }


    /**
     * @return \Illuminate\Support\Collection
     */
    public function offers()
    {
        $priyojonOffers = DB::table('partner_offers as po')
            ->where('po.is_active',1)
            ->join('partners as p', 'po.partner_id', '=', 'p.id')
            ->join('partner_categories as pc', 'p.partner_category_id', '=', 'pc.id') // you may add more joins
            ->select('po.*', 'pc.name_en AS offer_type_en', 'pc.name_bn AS offer_type_bn', 'p.company_name_en','p.company_name_bn','p.company_logo')
            ->orderBy('po.display_order')
            ->get();

        return $priyojonOffers;
    }

}