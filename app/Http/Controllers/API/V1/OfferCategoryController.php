<?php

namespace App\Http\Controllers\API\V1;

use App\Models\DurationCategory;
use App\Models\OfferCategory;
use App\Models\ProductDetail;
use App\Models\SimCategory;
use App\Models\Tag;
use App\Models\TagCategory;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PartnerOffer;
use App\Models\Partner;
use App\Models\Product;
use DB;
use Carbon\Carbon;

class OfferCategoryController extends Controller
{
    protected $response = [];

    public function __construct()
    {
        $this->response = [
            'status' => 200,
            'success' => true,
            'message' => 'Data Found!',
            'data' => []
        ];
    }

//    public function bindDynamicValues($obj, $json_data = 'other_attributes')
//    {
//        if (!empty($obj->{$json_data})) {
//            foreach ($obj->{$json_data} as $key => $value) {
//                $obj->{$key} = $value;
//            }
//        }
//        unset($obj->{$json_data});
//    }

    public function getPartnerOffersData()
    {
//        $data = PartnerOffer::where('is_active',1)
//                            ->with(['partner'=>function($query){
//                                $query->with('PartnerCategory:id,name_en,name_bn')->select();
//                            }
//                            ])->get();

        $data = DB::table('partner_offers as po')->where('is_active', 1)
            ->join('partners as p', 'po.partner_id', '=', 'p.id')
            ->join('partner_categories as pc', 'p.partner_category_id', '=', 'pc.id')// you may add more joins
            ->select('po.*', 'pc.name_en AS offer_type_en', 'pc.name_bn AS offer_type_bn', 'p.company_name_en', 'p.company_name_bn', 'p.company_logo')
            ->get();
        return $data;
    }

    public function offers($type)
    {
        $mytime = Carbon::now('Asia/Dhaka');
        $dateTime = $mytime->toDateTimeString();
        $currentSecends = strtotime($dateTime);


//        $query = Product::query();
//
//        $query->where('status', 1);
//        $query->where('start_date', '<=', $currentSecends);
//        $query->whereNull('end_date');
//        $products =  $query->orWhere('end_date', '>=', $currentSecends)->category($type)->get();
        // $products =  $query->whereNull('end_date')->category($type)->get();

        $products = Product::where('status', 1)
            ->where('start_date', '<=', $currentSecends)
            ->whereNull('end_date')
            ->orWhere('end_date', '>=', $currentSecends)
            ->category($type)
            ->get();

        foreach ($products as $product) {
            $this->bindDynamicValues($product, 'offer_info');
        }
        $this->response['data'] = $products;
        return response()->json($this->response);
    }

    public function index()
    {
        return response()->json(
            [
                'status' => 200,
                'success' => true,
                'message' => 'Data Found!',
                'data' => $this->getPartnerOffersData()
            ]
        );
    }


    public function offerCategories()
    {
        $tags = TagCategory::all();
        $sim = SimCategory::all();
        $offer = OfferCategory::where('parent_id', 0)->with('children')->get();
        $duration = DurationCategory::all();

        return response()->json(
            [
                'status' => 200,
                'success' => true,
                'message' => 'Data Found!',
                'data' => [
                    'tag' => $tags,
                    'sim' => $sim,
                    'offer' => $offer,
                    'duration' => $duration
                ]
            ]
        );
    }


    /**
     * @param $products
     * @return array
     */


    public function offerDetails($id)
    {
        try {

            $productDetail = PartnerOffer::select('id', 'partner_id')->where('id', $id)
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

            if (isset($productDetail)) {
                return response()->success($productDetail, 'Data Found!');
            }

            return response()->error('Data Not Found!');

        } catch (QueryException $e) {
            return response()->error('Data Not Found!', $e->getMessage());
        }
    }

}
