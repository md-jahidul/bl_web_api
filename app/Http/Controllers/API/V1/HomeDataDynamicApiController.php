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

class HomeDataDynamicApiController extends Controller
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


    public function factoryComponent($type,$id)
    {
        $data = null;
        switch ($type) {
            case "slider":
                $data = $this->getSliderData($id);
                break;
            case "recharge":
                $data = $this->getRechargeData();  
                break;
            case "quicklaunch":
                $data = $this->getQuickLaunchData();
                break;
            default:
                $data = "No suitable component found";
        }

        return $data;
    }


    public function getHomeData()
    {
        try{

            $componentList = (object)[
                [
                    'component_type' => 'slider',
                    'component_id'   =>  1,
                    'component_status' => 'enabled'
                ],
                [
                    'component_type' => 'recharge',
                    'component_id'   =>  null,
                    'component_status' => 'enabled'
                ],
                [
                    'component_type' => 'quicklaunch',
                    'component_id'   =>  null,
                    'component_status' => 'enabled'
                ],
                [
                    'component_type' => 'slider',
                    'component_id'   =>  2,
                    'component_status' => 'enabled'
                ],
                [
                    'component_type' => 'slider',
                    'component_id'   =>  3,
                    'component_status' => 'enabled'
                ]
            ];


            $componentList = json_decode(json_encode($componentList));

            $homePageData = [];
            foreach ($componentList as $component) {
                if($component->component_status == 'enabled'){
                    $homePageData[] = $this->factoryComponent($component->component_type, $component->component_id);
                }
            }

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
