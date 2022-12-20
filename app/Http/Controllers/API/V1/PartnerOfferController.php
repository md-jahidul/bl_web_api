<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Resources\PartnerOfferResource;
use App\Models\DurationCategory;
use App\Models\OfferCategory;
use App\Models\ProductDetail;
use App\Models\SimCategory;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Services\PartnerOfferService;
use App\Traits\FileTrait;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PartnerOffer;
use App\Models\Partner;
use App\Models\Product;
use DB;
use Carbon\Carbon;

class PartnerOfferController extends Controller {
    /**
     * @var PartnerOfferService
     */
    private $partnerOfferService;

    public function __construct(PartnerOfferService $partnerOfferService) {
        $this->partnerOfferService = $partnerOfferService;
    }

    public function getPartnerOffersData() {
        $data = DB::table('partner_offers as po')->where('is_active', 1)
                ->join('partners as p', 'po.partner_id', '=', 'p.id')
                ->join('partner_categories as pc', 'p.partner_category_id', '=', 'pc.id')// you may add more joins
                ->select('po.*', 'pc.name_en AS offer_type_en', 'pc.name_bn AS offer_type_bn', 'p.company_name_en', 'p.company_name_bn', 'p.company_logo')
                ->get();
        return PartnerOfferResource::collection($data);
    }

    public function index() {
        return response()->json(
                        [
                            'status' => 200,
                            'success' => true,
                            'message' => 'Data Found!',
                            'data' => $this->getPartnerOffersData()
                        ]
        );
    }

    /**
     * @param $slug
     * @return array
     */
    public function offerDetails($slug): array
    {
        return $this->partnerOfferService->partnerOfferDetails($slug);
        /*        try {
            $productDetail = PartnerOffer::
            select(
                'id',
                'partner_id',
                'partner_category_id',
                'loyalty_tier_id',
                'card_img',
                'validity_en',
                'validity_bn',
                'offer_unit',
                'offer_value',
                'offer_scale'
            )
            ->with([
                'partner_offer_details' => function($q){
//                    $q->select('id', 'name_en', 'name_bn');
                },
                'offer_category' => function($q){
                    $q->select('id', 'name_en', 'name_bn');
                },
                'partner' => function ($q){
                    $q->select('id', 'company_logo', 'company_name_en', 'company_name_bn');
                }
            ])
            ->first();


            $productDetail = PartnerOffer::
            select(
                'partner_offers.*',
                'a.area_en',
                'a.area_bn',
                'p.company_name_en',
                'p.company_name_bn',
                'pc.name_en',
                'pc.name_bn'
            )
            ->LeftJoin('partner_area_list as a', 'partner_offers.area_id', '=', 'a.id')
            ->LeftJoin('partners as p', 'p.id', '=', 'partner_offers.partner_id')
            ->LeftJoin('partner_categories as pc', 'pc.id', '=', 'partner_offers.partner_category_id')
            ->where('partner_offers.url_slug', $slug)
            ->orWhere('partner_offers.url_slug_bn', $slug)
            ->orWhere('partner_offers.id', $slug)
            ->with([
                'partner_offer_details',
                'offer_category' => function($q){
                    $q->select('id', 'name_en', 'name_bn');
                },
                'partner' => function ($query) {
                    $query->select([
                        'id',
                        'contact_person_mobile',
                        'company_address',
                        'company_website',
                        'google_play_link',
                        'apple_app_store_link']);
                }])
            ->first();

            $data = [];
            if (isset($productDetail)) {
                $data['id'] = $productDetail->id;
                $data['company_name_en'] = $productDetail->company_name_en;
                $data['company_name_bn'] = $productDetail->company_name_bn;
                $data['validity_en'] = $productDetail->validity_en;
                $data['validity_bn'] = $productDetail->validity_bn;
                $data['offer_scale'] = $productDetail->offer_scale;
                $data['offer_value'] = $productDetail->offer_value;
                $data['offer_unit'] = $productDetail->offer_unit;
                $data['start_date'] = $productDetail->start_date;
                $data['end_date'] = $productDetail->end_date;
                $data['get_offer_msg_en'] = $productDetail->get_offer_msg_en;
                $data['get_offer_msg_bn'] = $productDetail->get_offer_msg_bn;
                $data['btn_text_en'] = $productDetail->btn_text_en;
                $data['btn_text_bn'] = $productDetail->btn_text_bn;

                $data['eligible_customer_en'] = $productDetail->partner_offer_details->eligible_customer_en;
                $data['eligible_customer_bn'] = $productDetail->partner_offer_details->eligible_customer_bn;
                $data['top_details_en'] = $productDetail->partner_offer_details->details_en;
                $data['top_details_bn'] = $productDetail->partner_offer_details->details_bn;
                $data['offer_details_en'] = $productDetail->partner_offer_details->offer_details_en;
                $data['offer_details_bn'] = $productDetail->partner_offer_details->offer_details_bn;
                $data['avail_en'] = $productDetail->partner_offer_details->avail_en;
                $data['avail_bn'] = $productDetail->partner_offer_details->avail_bn;

                $phone = json_decode($productDetail->phone);

                $data['phone_en'] = !empty($phone) ? $phone->en : "";
                $data['phone_bn'] = !empty($phone) ? $phone->bn : "";

                $location = json_decode($productDetail->location);

                $data['location_en'] = !empty($location) ? $location->en : "";
                $data['location_bn'] = !empty($location) ? $location->bn : "";
                $data['area_en'] = $productDetail->area_en;
                $data['area_bn'] = $productDetail->area_bn;

                $data['page_header'] = $productDetail->page_header;
                $data['page_header_bn'] = $productDetail->page_header_bn;
                $data['schema_markup'] = $productDetail->schema_markup;
                $data['url_slug'] = $productDetail->url_slug;
                $data['url_slug_bn'] = $productDetail->url_slug_bn;

                $data['banner_image_url'] = $productDetail->partner_offer_details->banner_image_url;
                $data['banner_alt_text'] = $productDetail->partner_offer_details->banner_alt_text;
                $data['apple_app_store_link'] = $productDetail->partner->apple_app_store_link;
                $data['google_play_link'] = $productDetail->partner->google_play_link;
                $data['company_website'] = $productDetail->partner->company_website;
                $data['category_tag_en'] = $productDetail->partner->name_en;
                $data['category_tag_bn'] = $productDetail->partner->name_bn;
//                $data['map_link'] = $productDetail->map_link;
//                $data['like'] = $productDetail->like;

                return response()->success($data, 'Data Found!');
            }

            return response()->error('Data Not Found!');
        } catch (QueryException $e) {
            return response()->error('Data Not Found!', $e);
        }*/
    }

}
