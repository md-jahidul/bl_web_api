<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Resources\PartnerOfferResource;
use App\Models\DurationCategory;
use App\Models\OfferCategory;
use App\Models\ProductDetail;
use App\Models\SimCategory;
use App\Models\Tag;
use App\Models\TagCategory;
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

    use FileTrait;

    protected $response = [];

    public function __construct() {
        $this->response = [
            'status' => 200,
            'success' => true,
            'message' => 'Data Found!',
            'data' => []
        ];
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

    public function showFile($dirLocation, $fileName)
    {
        $fileName = explode('.', $fileName)[0];

        $decode = base64_decode($dirLocation);

//        $offers = OfferCategory::where('banner_alt_text', $fileName)->first();
//        return $this->view($offers->banner_image_url);

        return $this->view($decode);
    }

    public function offerCategories() {
        $tags = TagCategory::all();
        $sim = SimCategory::all();
        $offer = OfferCategory::where('parent_id', 0)->with('children')->get();


        if (!empty($offer)) {
            $offer_final = array_map(function($value) {
                if (!empty($value['banner_image_url'])) {

                    $encrypted = base64_encode($value['banner_image_url']);

                    $extension = explode('.', $value['banner_image_url']);
                    $extension = isset($extension[1]) ? ".".$extension[1] : null;
                    $fileName = $value['banner_alt_text'] . $extension;


                    $value['banner_image_url'] = request()->root() . "/api/v1/show-file/$encrypted/" . $fileName;
//                    $value['banner_image_url'] = config('filesystems.image_host_url') . $value['banner_image_url'];
                }
                if (!empty($value['banner_image_mobile'])) {
                    $value['banner_image_mobile'] = config('filesystems.image_host_url') . $value['banner_image_mobile'];
                }
                return $value;
            }, $offer->toArray());
        } else {
            $offer_final = [];
        }

        $duration = DurationCategory::all();

        return response()->json(
                        [
                            'status' => 200,
                            'success' => true,
                            'message' => 'Data Found!',
                            'data' => [
                                'tag' => $tags,
                                'sim' => $sim,
                                'offer' => $offer_final,
                                'duration' => $duration
                            ]
                        ]
        );
    }

    /**
     * @param $products
     * @return array
     */
    public function offerDetails($id) {
        try {

            $productDetail = PartnerOffer::select('partner_offers.*', 'a.area_en', 'a.area_bn', 'p.company_name_en', 'p.company_name_bn')
                    ->LeftJoin('partner_area_list as a', 'partner_offers.area_id', '=', 'a.id')
                    ->LeftJoin('partners as p', 'p.id', '=', 'partner_offers.partner_id')
                    ->where('partner_offers.id', $id)
                    ->with(['partner_offer_details', 'partner' => function ($query) {
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

                $banner = "";
                if($productDetail->partner_offer_details->banner_image_url != ""){
                   $banner = config('filesystems.image_host_url') . $productDetail->partner_offer_details->banner_image_url;
                }
                $data['banner_image_url'] = $banner;
                $data['banner_alt_text'] = $productDetail->partner_offer_details->banner_alt_text;
                $data['apple_app_store_link'] = $productDetail->partner->apple_app_store_link;
                $data['google_play_link'] = $productDetail->partner->google_play_link;
                $data['company_website'] = $productDetail->partner->company_website;
                $data['map_link'] = $productDetail->map_link;
                $data['like'] = $productDetail->like;

                return response()->success($data, 'Data Found!');
            }

            return response()->error('Data Not Found!');
        } catch (QueryException $e) {
            return response()->error('Data Not Found!', $e);
        }
    }

}
