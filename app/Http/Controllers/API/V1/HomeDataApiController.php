<?php

namespace App\Http\Controllers\API\V1;

use App\Models\QuickLaunch;
use App\Models\QuickLaunchItem;
use App\Models\AlSlider;
use App\Models\AlSliderComponentType;
use App\Models\AlSliderImage;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HomeDataApiController extends Controller
{
    public function getSliderData($id){
        $slider_images = AlSliderImage::where('slider_id',$id)->orderBy('display_order')->get();

        foreach ($slider_images as $slider_image){
            if(!empty($slider_image->other_attributes)){
                $slider_image->other_attributes = json_decode( $slider_image->other_attributes );
                foreach ($slider_image->other_attributes as $key => $value){
                    $slider_image->{$key} = $value;
                }
            }
            unset($slider_image->other_attributes);
        }

        $slider = AlSlider::find($id);

//        return $slider;

        if(!empty($slider->other_attributes)){
            $slider->other_attributes = json_decode( $slider->other_attributes );
            foreach ($slider->other_attributes as $key => $value){
                $slider->{$key} = $value;
            }
            unset($slider->other_attributes);
        }

        $slider->component = AlSliderComponentType::find($slider->component_id)->slug;
        $slider->data = $slider_images;
        return $slider;
    }

    public function getHomeData()
    {
        try{

            $quickLaunch = QuickLaunchItem::orderBy('display_order')->get();

            $heroSlider = $this->getSliderData(1);
            $digitalServiceSlider = $this->getSliderData(2);
            $testimonialSlider = $this->getSliderData(3);
            $homePageData = [
                $heroSlider,
                [
                    "id"=> 1,
                    "title"=> "MOBILE RECHARGE & POSTPAID BILL PAYMENT",
                    "description"=> "",
                    "component"=> "RechargeAndServices",
                    "data" => []
                ],
                [
                    "component"=> "QuickLaunch",
                    "data" => $quickLaunch
                ],
//                [
//                    "id"=> 2,
//                    "title"=> "Home page exple device",
//                    "description"=> "",
//                    "component"=> "ExploreDevices",
//                    "data" => []
//                        [
//                            "title"=> "Find the best deals from our eshop and enjoy exclusive offers!",
//                            "short_note"=> "iPhone XR",
//                            "description"=> "Brilliant. In every way.",
//                            "image_url"=> "https://www.banglalink.net/sites/default/files/Home-Banner-1920-X-870_0.jpg",
//                            "alt_text"=> "Explore device => samsung",
//                            "action_btn_label"=> "Show now",
//                            "action_btn_url"=> "https://www.banglalink.net/en/business/business-solutions/m-connex",
//                            "is_external_url"=> true
//                        ],
//                        [
//                            "title"=> "Find the best deals from our eshop and enjoy exclusive offers!",
//                            "short_note"=> "iPhone XR",
//                            "description"=> "Brilliant. In every way.",
//                            "image_url"=> "https://www.banglalink.net/sites/default/files/Home-Banner-1920-X-870_0.jpg",
//                            "alt_text"=> "Explore device => samsung",
//                            "action_btn_label"=> "Show now",
//                            "action_btn_url"=> "https://www.banglalink.net/en/business/business-solutions/m-connex",
//                            "is_external_url"=> true
//                        ],
//                        [
//                            "title"=> "Find the best deals from our eshop and enjoy exclusive offers!",
//                            "short_note"=> "iPhone XR",
//                            "description"=> "Brilliant. In every way.",
//                            "image_url"=> "https://www.banglalink.net/sites/default/files/Home-Banner-1920-X-870_0.jpg",
//                            "alt_text"=> "Explore device => samsung",
//                            "action_btn_label"=> "Show now",
//                            "action_btn_url"=> "https://www.banglalink.net/en/business/business-solutions/m-connex",
//                            "is_external_url"=> true
//                        ]
//                    ]
//                ],
                $digitalServiceSlider,
                $testimonialSlider,

            ];

            if (isset($homePageData)) {
                return response()->json(
                    [
                        'status' => 200,
                        'success' => true,
                        'message' => 'Data Found!',
                        'data' => $homePageData
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
