<?php

namespace App\Http\Controllers\API\V1;

use App\Models\QuickLaunch;
use App\Models\QuickLaunchItem;
use App\Models\Slider;
use App\Models\SliderComponentType;
use App\Models\SliderImage;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HomeDataApiController extends Controller
{
    public function getSliderData($id){
        $slider_images = SliderImage::where('slider_id',$id)->orderBy('sequence')->get();

        foreach ($slider_images as $slider_image){
            if(!empty($slider_image->other_attributes)){
                $slider_image->other_attributes = json_decode( $slider_image->other_attributes );

                foreach ($slider_image->other_attributes as $key => $value){
                    $slider_image->{$key} = $value;
                }
            }
            unset($slider_image->other_attributes);
        }


        $slider = Slider::find($id);
        $slider->component = SliderComponentType::find($slider->component_id)->slug;
        $slider->data = $slider_images;
        return $slider;
    }

    public function getHomeData()
    {
        try{

            $quickLaunch = QuickLaunchItem::orderBy('display_order')->get();

            $heroSlider = $this->getSliderData(1);
            $digitalServiceSlider = $this->getSliderData(2);

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
                [
                    "id"=> 2,
                    "title"=> "Home page exple device",
                    "description"=> "",
                    "component"=> "ExploreDevices",
                    "data" => [
                        [
                            "title"=> "Find the best deals from our eshop and enjoy exclusive offers!",
                            "short_note"=> "iPhone XR",
                            "description"=> "Brilliant. In every way.",
                            "image_url"=> "https://www.banglalink.net/sites/default/files/Home-Banner-1920-X-870_0.jpg",
                            "alt_text"=> "Explore device => samsung",
                            "action_btn_label"=> "Show now",
                            "action_btn_url"=> "https://www.banglalink.net/en/business/business-solutions/m-connex",
                            "is_external_url"=> true
                        ],
                        [
                            "title"=> "Find the best deals from our eshop and enjoy exclusive offers!",
                            "short_note"=> "iPhone XR",
                            "description"=> "Brilliant. In every way.",
                            "image_url"=> "https://www.banglalink.net/sites/default/files/Home-Banner-1920-X-870_0.jpg",
                            "alt_text"=> "Explore device => samsung",
                            "action_btn_label"=> "Show now",
                            "action_btn_url"=> "https://www.banglalink.net/en/business/business-solutions/m-connex",
                            "is_external_url"=> true
                        ],
                        [
                            "title"=> "Find the best deals from our eshop and enjoy exclusive offers!",
                            "short_note"=> "iPhone XR",
                            "description"=> "Brilliant. In every way.",
                            "image_url"=> "https://www.banglalink.net/sites/default/files/Home-Banner-1920-X-870_0.jpg",
                            "alt_text"=> "Explore device => samsung",
                            "action_btn_label"=> "Show now",
                            "action_btn_url"=> "https://www.banglalink.net/en/business/business-solutions/m-connex",
                            "is_external_url"=> true
                        ]
                    ]
                ],
                $digitalServiceSlider,
//                [
//                    "id"=> 3,
//                    "title"=> "Home page digital services slider",
//                    "description"=> "",
//                    "shortcode"=> "DigitalServices",
//                    "data" => [
//                        [
//                            "title"=> "Banglaflix",
//                            "description"=> "Mobile TV brings live TV &amp; Video on Demand (VOD) streaming on a mobile phone.",
//                            "short_note"=> "Monthly ৳ 50",
//                            "image_url"=> "https://www.banglalink.net/sites/default/files/Home-Banner-1920-X-870_0.jpg",
//                            "alt_text"=> "Degital Service Banglaflix",
//                            "google_play_url"=> "https://play.google.com/store/apps/details?id=com.ebs.banglaflix",
//                            "apple_store_url"=> "https://apps.apple.com/us/app/banglaflix/id1124030141",
//                        ],
//                        [
//                            "title"=> "Mobile Tv",
//                            "description"=> "Mobile TV brings live TV &amp; Video on Demand (VOD) streaming on a mobile phone.",
//                            "short_note"=> "Monthly ৳ 50",
//                            "image_url"=> "https://www.banglalink.net/sites/default/files/Home-Banner-1920-X-870_0.jpg",
//                            "alt_text"=> "Degital Service Banglaflix",
//                            "google_play_url"=> "https://play.google.com/store/apps/details?id=com.ebs.banglaflix",
//                            "apple_store_url"=> "https://apps.apple.com/us/app/banglaflix/id1124030141",
//                        ],
//                        [
//                            "title"=> "Gaan Mela",
//                            "description"=> "Mobile TV brings live TV &amp; Video on Demand (VOD) streaming on a mobile phone.",
//                            "short_note"=> "Monthly ৳ 50",
//                            "image_url"=> "https://www.banglalink.net/sites/default/files/Home-Banner-1920-X-870_0.jpg",
//                            "alt_text"=> "Degital Service Banglaflix",
//                            "google_play_url"=> "https://play.google.com/store/apps/details?id=com.ebs.banglaflix",
//                            "apple_store_url"=> "https://apps.apple.com/us/app/banglaflix/id1124030141",
//                        ],
//                        [
//                            "title"=> "Boi Ghar",
//                            "description"=> "Mobile TV brings live TV &amp; Video on Demand (VOD) streaming on a mobile phone.",
//                            "short_note"=> "Monthly ৳ 50",
//                            "image_url"=> "https://www.banglalink.net/sites/default/files/Home-Banner-1920-X-870_0.jpg",
//                            "alt_text"=> "Degital Service Banglaflix",
//                            "google_play_url"=> "https://play.google.com/store/apps/details?id=com.ebs.banglaflix",
//                            "apple_store_url"=> "https://apps.apple.com/us/app/banglaflix/id1124030141",
//                        ],
//                        [
//                            "title"=> "Others",
//                            "description"=> "Mobile TV brings live TV &amp; Video on Demand (VOD) streaming on a mobile phone.",
//                            "short_note"=> "Monthly ৳ 50",
//                            "image_url"=> "https://www.banglalink.net/sites/default/files/Home-Banner-1920-X-870_0.jpg",
//                            "alt_text"=> "Degital Service Banglaflix",
//                            "google_play_url"=> "https://play.google.com/store/apps/details?id=com.ebs.banglaflix",
//                            "apple_store_url"=> "https://apps.apple.com/us/app/banglaflix/id1124030141",
//                        ]
//                    ]
//                ],
                [
                    "id"=> 1,
                    "title"=> "Testimonial",
                    "description"=> "asdf sdfsdf sdf",
                    "component"=> "Testimonial",
                    "data" => [
                        [
                            "title"=> "Shahriar Ahmed",
                            "description"=> "Banglalink provide the fastest internet throughout the country, I never get the best experience except using Banlalink. It’s awesome service ever, I’ll always use Banglalink.",
                            "short_note"=> "Studiomaqs",
                            "image_url"=> "https://www.banglalink.net/sites/default/files/Home-Banner-1920-X-870_0.jpg",
                            "alt_text"=> "Banglalink clients",
                            "ratings"=> 4.5
                        ],
                        [
                            "title"=> "Shahriar Ahmed",
                            "description"=> "Banglalink provide the fastest internet throughout the country, I never get the best experience except using Banlalink. It’s awesome service ever, I’ll always use Banglalink.",
                            "short_note"=> "Studiomaqs",
                            "image_url"=> "https://www.banglalink.net/sites/default/files/Home-Banner-1920-X-870_0.jpg",
                            "alt_text"=> "Banglalink clients",
                            "ratings"=> 4.5
                        ],
                        [
                            "title"=> "Shahriar Ahmed",
                            "description"=> "Banglalink provide the fastest internet throughout the country, I never get the best experience except using Banlalink. It’s awesome service ever, I’ll always use Banglalink.",
                            "short_note"=> "Studiomaqs",
                            "image_url"=> "https://www.banglalink.net/sites/default/files/Home-Banner-1920-X-870_0.jpg",
                            "alt_text"=> "Banglalink clients",
                            "ratings"=> 4.5
                        ],
                        [
                            "title"=> "Shahriar Ahmed",
                            "description"=> "Banglalink provide the fastest internet throughout the country, I never get the best experience except using Banlalink. It’s awesome service ever, I’ll always use Banglalink.",
                            "short_note"=> "Studiomaqs",
                            "image_url"=> "https://www.banglalink.net/sites/default/files/Home-Banner-1920-X-870_0.jpg",
                            "alt_text"=> "Banglalink clients",
                            "ratings"=> 4.5
                        ]
                    ]
                ]
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
