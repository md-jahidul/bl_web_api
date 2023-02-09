<?php

namespace App\Repositories;

use App\Models\Partner;
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

    public function getCategories()
    {
        $category = Partner::select('c.id', 'c.name_en', 'c.name_bn')
            ->leftJoin('partner_categories as c', 'c.id', 'partners.partner_category_id')
            ->join('partner_offers as o', 'o.partner_id', '=', 'partners.id')->groupBy('partners.partner_category_id')->where('o.is_active', 1)->get();
        return $category;
    }

    public function getAreas()
    {
        $areas = $this->model::select('a.id', 'a.area_en', 'a.area_bn')
            ->join('partner_area_list as a', 'a.id', '=', 'partner_offers.area_id')
            ->groupBy('a.id')->where('partner_offers.is_active', 1)->get();
        return $areas;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function offers()
    {
        $priyojonOffers = DB::table('partner_offers as po')
            ->where('po.is_active', 1)
            ->join('partners as p', 'po.partner_id', '=', 'p.id')
            ->LeftJoin('partner_area_list as a', 'po.area_id', '=', 'a.id')
            ->join('partner_categories as pc', 'p.partner_category_id', '=', 'pc.id') // you may add more joins
            ->select(
                'po.*',
                'a.area_en',
                'a.area_bn',
                'p.company_name_en',
                'p.company_name_bn',
                'p.company_logo',
                'pc.name_en AS offer_type_en',
                'pc.name_bn AS offer_type_bn',
                'pc.page_header',
                'pc.page_header_bn',
                'pc.schema_markup',
                'pc.url_slug_en',
                'pc.url_slug_bn'
            )
            ->orderBy('po.display_order')
            ->get();

        return $priyojonOffers;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function allOffers($page, $elg, $area, $searchStr)
    {
        $actualPage = $page - 1;
        $limit = 9;
        $offset = $actualPage * $limit;
        $q = $this->model->where('is_active', 1)
            ->select(
                'id',
                'partner_id',
                'partner_category_id',
                'loyalty_tier_id',
                'card_img',
                'validity_en',
                'validity_bn',
                'btn_text_en',
                'btn_text_bn',
                'url_slug',
                'url_slug_bn',
                'page_header',
                'page_header_bn',
                'schema_markup',
                'other_attributes'
            )
            ->whereHas('partner', function ($q) use ($searchStr) {
                if ($searchStr != "") {
                    $q->whereRaw("company_name_en Like '%$searchStr%'");
                    $q->whereRaw("company_name_bn Like '%$searchStr%'");
                }
            })
            ->with(['partner' => function ($q) use ($searchStr) {
                if ($searchStr != "") {
                    $q->whereRaw("company_name_en Like '%$searchStr%'");
                    $q->whereRaw("company_name_bn Like '%$searchStr%'");
                }
            }]);
        if ($elg != "") {
            $q->where('loyalty_tier_id', $elg);
        }
        if ($area != "") {
            $q->where('area_id', $area);
        }

        $priyojonOffers = $q
            ->offset($offset)->limit($limit)->get();
        return $priyojonOffers;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function discountOffers($page, $elg, $cat, $area, $searchStr)
    {
        $actualPage = $page - 1;
        $limit = 9;
        $offset = $actualPage * $limit;
        $offers = DB::table('partner_offers as po')
            ->where('po.is_campaign', 0)
            ->where('po.is_active', 1)
            ->join('partners as p', 'po.partner_id', '=', 'p.id')
            ->LeftJoin('partner_area_list as a', 'po.area_id', '=', 'a.id')
            ->join('partner_categories as pc', 'p.partner_category_id', '=', 'pc.id') // you may add more joins
            ->select(
                'po.*',
                'a.area_en',
                'a.area_bn',
                'pc.name_en AS offer_type_en',
                'pc.name_bn AS offer_type_bn',
                'p.company_name_en',
                'p.company_name_bn',
                'p.company_logo',
                'pc.name_en AS offer_type_en',
                'pc.name_bn AS offer_type_bn',
                'pc.page_header',
                'pc.page_header_bn',
                'pc.schema_markup',
                'pc.url_slug_en',
                'pc.url_slug_bn'
            )
            ->orderBy('po.display_order')
            ->offset($offset)->limit($limit);

        if ($elg != "") {
            // $elg == 1 ? $offers->where('po.silver', 1) : null;
            // $elg == 2 ? $offers->where('po.gold', 1) : null;
            // $elg == 3 ? $offers->where('po.platium', 1) : null;
            $offers->where('po.loyalty_tier_id', $elg);
        }

        if ($cat != "") {
            $offers->where('p.partner_category_id', $cat);
        }
        if ($area != "") {
            $offers->where('po.area_id', $area);
        }
        if ($searchStr != "") {
            $offers->whereRaw("p.company_name_en Like '%$searchStr%'");
        }


        $priyojonOffers = $offers->get();

        return $priyojonOffers;
    }

    public function campaignOffers()
    {
        return DB::table('partner_offers as po')
            ->where('po.is_active', 1)
            ->where('po.is_campaign', 1)
            ->join('partners as p', 'po.partner_id', '=', 'p.id')
            ->select(
                'po.campaign_img',
                'po.offer_scale',
                'po.offer_value',
                'po.offer_unit',
                'po.validity_en',
                'po.validity_bn',
                'po.like',
                'po.get_offer_msg_en',
                'po.get_offer_msg_bn',
                'po.btn_text_en',
                'po.btn_text_bn',
                'po.alt_text_en',
                'po.alt_text_bn',
                'po.url_slug',
                'po.url_slug_bn',
                'po.other_attributes',
                'p.company_name_en',
                'p.company_name_bn',
                'p.company_logo'
            )
            ->orderBy('po.display_order')
            ->get();
    }

    public function offerDetails($slug)
    {
        return DB::table('partner_offers as po')
            ->where('po.is_active', 1)
            ->where('po.url_slug', $slug)
            ->orWhere('po.url_slug_bn', $slug)
            ->orWhere('po.id', $slug)
            ->join('partners as p', 'po.partner_id', '=', 'p.id')
            ->LeftJoin('partner_offer_details as pod', 'pod.partner_offer_id', '=', 'po.id')
            ->LeftJoin('partner_area_list as a', 'po.area_id', '=', 'a.id')
            ->join('partner_categories as pc', 'p.partner_category_id', '=', 'pc.id') // you may add more joins
            ->select(
                'po.*',
                'a.area_en',
                'a.area_bn',
                'p.company_website',
                'p.company_name_en',
                'p.company_name_bn',
                'p.company_logo',
                'pc.name_en AS offer_type_en',
                'pc.name_bn AS offer_type_bn',
                'pc.page_header',
                'pc.page_header_bn',
                'pc.schema_markup',
                'pc.url_slug_en',
                'pc.url_slug_bn',
                'offer_details_en',
                'offer_details_bn',
                'avail_en',
                'avail_bn',
                'banner_image_url',
                'eligible_customer_en',
                'eligible_customer_bn'
            )
            ->orderBy('po.display_order')
            ->first();
    }
}
