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

class PartnerOfferController extends Controller
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
        

        if( !empty($offer) ){
            $offer_final = array_map(function($value){
            if( !empty($value['banner_image_url']) ){
                $value['banner_image_url'] = config('filesystems.image_host_url') . $value['banner_image_url'];
            }
            return $value;
            }, $offer->toArray());
        }
        else{
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
