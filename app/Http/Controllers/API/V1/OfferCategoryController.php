<?php

namespace App\Http\Controllers\API\V1;

use App\Models\DurationCategory;
use App\Models\OfferCategory;
use App\Models\SimCategory;
use App\Models\Tag;
use App\Models\TagCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PartnerOffer;
use App\Models\Partner;
use App\Models\Product;
use DB;

class OfferCategoryController extends Controller
{
    protected $response = [];

    public function __construct()
    {
        $this->response = [
            'status' => 200,
            'success' => true,
            'message' => 'Data Found!',
            'data' =>  []
        ];
    }

    public function bindDynamicValues($obj, $json_data = 'other_attributes')
    {
        if(!empty($obj->{ $json_data }))
        {
            foreach ($obj->{ $json_data } as $key => $value){
                $obj->{$key} = $value;
            }
        }
        unset($obj->{ $json_data });
    }

    public function getPartnerOffersData()
    {
//        $data = PartnerOffer::where('is_active',1)
//                            ->with(['partner'=>function($query){
//                                $query->with('PartnerCategory:id,name_en,name_bn')->select();
//                            }
//                            ])->get();

        $data = DB::table('partner_offers as po')->where('is_active',1)
                        ->join('partners as p', 'po.partner_id', '=', 'p.id')
                        ->join('partner_categories as pc', 'p.partner_category_id', '=', 'pc.id') // you may add more joins
                        ->select('po.*', 'pc.name_en AS offer_type_en', 'pc.name_bn AS offer_type_bn', 'p.company_name_en','p.company_name_bn','p.company_logo')
                        ->get();
        return $data;
    }

    public function offers($type)
    {
        $products = Product::category($type)->get();
        foreach ( $products as $product){
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
        $sim  = SimCategory::all();
        $offer  = OfferCategory::where('parent_id', 0)->with('children')->get();
        $duration  = DurationCategory::all();

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

    public function productDetails($type, $id)
    {
        $productDetail = Product::where('id',$id)
            ->category($type)
            ->with('product_details', 'related_product')
            ->first();

        $this->bindDynamicValues($productDetail, 'offer_info');

        $data = [];
        foreach ($productDetail->related_product as $product)
        {
            $findProduct = Product::findOrFail($product->related_product_id);
            array_push($data, $findProduct);
        }

        $productDetail->related_products = $data;

        $this->bindDynamicValues($productDetail->related_products, 'offer_info');
//        return $productDetail->related_products;

        unset($productDetail->related_product);
        return response()->json(
            [
                'status' => 200,
                'success' => true,
                'message' => 'Data Found!',
                'data' => $productDetail
            ]
        );
    }
}
