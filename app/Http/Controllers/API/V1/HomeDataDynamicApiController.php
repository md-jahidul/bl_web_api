<?php

namespace App\Http\Controllers\API\V1;

use App\Models\QuickLaunch;
use App\Models\QuickLaunchItem;
use App\Models\AlSlider;
use App\Models\AlSliderComponentType;
use App\Models\AlSliderImage;
use App\Models\ShortCode;
use App\Models\PartnerOffer;
use App\Models\MetaTag;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class HomeDataDynamicApiController extends Controller
{
    // In PHP, By default objects are passed as reference copy to a new Object.
    public function bindDynamicValues($obj)
    {
        if(!empty($obj->other_attributes)){
            $obj->other_attributes = $obj->other_attributes;
            foreach ($obj->other_attributes as $key => $value){
                $obj->{$key} = $value;
            }
        }
        unset($obj->other_attributes);
    }

    public function getSliderData($id){

        $slider = AlSlider::find($id);
        $component = AlSliderComponentType::find($slider->component_id)->slug;

        $limit = ( $component == 'Testimonial' ) ? 5 : false;

        $query = AlSliderImage::where('slider_id',$id)
                                ->where('is_active',1)
                                ->orderBy('display_order');

        $slider_images =  $limit ? $query->limit($limit)->get() : $query->get();

        foreach ($slider_images as $slider_image){
           $this->bindDynamicValues($slider_image);
        }

        $this->bindDynamicValues($slider);

        $slider->component = $component;
        $slider->data = $slider_images;
        return $slider;
    }

    public function getQuickLaunchData()
    {
        return  [
            "component"=> "QuickLaunch",
            "data" =>  QuickLaunchItem::orderBy('display_order')->get()
        ];
    }

    public function getRechargeData()
    {
        return [
            "id"=> 1,
            "title"=> "MOBILE RECHARGE & POSTPAID BILL PAYMENT",
            "description"=> "",
            "component"=> "RechargeAndServices",
            "data" => []
        ];
    }


    public function getPartnerOffersData($id)
    {
        $slider = AlSlider::find($id);

        if(!empty($slider->other_attributes)){
            $slider->other_attributes = $slider->other_attributes;
            foreach ($slider->other_attributes as $key => $value){
                $slider->{$key} = $value;
            }
            unset($slider->other_attributes);
        }

        $slider->component = AlSliderComponentType::find($slider->component_id)->slug;

        $slider->data = PartnerOffer::where('show_in_home',1)->where('is_active',1)
                                    ->with(['partner'=>function($query){
                                                $query->with('PartnerCategory:id,name_en,name_bn')->select();
                                            }
                                          ])->get();

        $slider->data = DB::table('partner_offers as po')
                        ->join('partners as p', 'po.partner_id', '=', 'p.id')
                        ->join('partner_categories as pc', 'p.partner_category_id', '=', 'pc.id') // you may add more joins
                        ->select('po.*', 'pc.name_en AS offer_type_en', 'pc.name_bn AS offer_type_bn', 'p.company_name_en','p.company_name_bn','p.company_logo')
                        ->get();



        return $slider;
    }


    public function factoryComponent($type,$id)
    {
        $data = null;
        switch ($type) {
            case "slider_single":
                $data = $this->getSliderData($id);
                break;
            case "recharge":
                $data = $this->getRechargeData();
                break;
            case "quicklaunch":
                $data = $this->getQuickLaunchData();
                break;
            case "slider_multiple":
                $data = $this->getPartnerOffersData($id);
                break;
            default:
                $data = "No suitable component found";
        }

        return $data;
    }


    public function getHomeData()
    {
        try{
            $componentList = ShortCode::where('page_id',1)
                                        ->where('is_active',1)
                                        ->get();

            $metainfo = MetaTag::where('page_id',1)
                                     ->first()->toArray();

            $homePageData = [];
            foreach ($componentList as $component) {
                $homePageData[] = $this->factoryComponent($component->component_type, $component->component_id);
            }

            if (isset($homePageData)) {
                return response()->json(
                    [
                        'status' => 200,
                        'success' => true,
                        'message' => 'Data Found!',
                        'data' => [
                            'metatags' => $metainfo,
                            'components' => $homePageData
                        ]
                    ]
                );
            }
            return response()->json(
                [
                    'status' => 400,
                    'success' => false,
                    'message' => 'Data Not Found!'
                ]
            );
        }catch (QueryException $e) {
            return response()->json(
                [
                    'status' => 403,
                    'success' => false,
                    'message' => explode('|', $e->getMessage())[0],
                ]
            );
        }
    }
}
