<?php

namespace App\Http\Controllers\API\V1;

use App\Models\QuickLaunch;
use App\Models\QuickLaunchItem;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HomeDataApiController extends Controller
{
    public function getHomeData()
    {
        try{

            $quickLaunch = QuickLaunchItem::all();
            $homePageData = [
                [
                    "id"=> 1,
                    "title"=> "Home page main slider",
                    "description"=> "",
                    "shortcode"=> "Slider",
                    "data" => [
                        [
                            "title"=> "Extra internet for all Banglalink users",
                            "description"=> "Banglalink is one of the leading digital communications service providers in Bangladesh working to unlock new opportunities for its customers as they navigate the digital world.",
                            "image_url"=> "https://www.banglalink.net/sites/default/files/Home-Banner-1920-X-870_0.jpg",
                            "thumb_image_url"=> "https://www.banglalink.net/sites/default/files/Home-Banner-1920-X-870_0.jpg",
                            "alt_text"=> "Internet offer image",
                            "action_btn_label"=> "Internet Offers",
                            "action_btn_url"=> "https://www.banglalink.net/offers",
                            "is_external_url"=> false
                        ],
                        [
                            "title"=> "Extra internet for all Banglalink users 1",
                            "description"=> "Banglalink is one of the leading digital communications service providers in Bangladesh working to unlock new opportunities for its customers as they navigate the digital world.",
                            "image_url"=> "https://www.banglalink.net/sites/default/files/Home-Banner-1920-X-870_0.jpg",
                            "thumb_image_url"=> "https://www.banglalink.net/sites/default/files/Home-Banner-1920-X-870_0.jpg",
                            "alt_text"=> "Internet offer image",
                            "action_btn_label"=> "Internet Offers",
                            "action_btn_url"=> "https://www.banglalink.net/offers",
                            "is_external_url"=> false
                        ],
                        [
                            "title"=> "Extra internet for all Banglalink users 2",
                            "description"=> "Banglalink is one of the leading digital communications service providers in Bangladesh working to unlock new opportunities for its customers as they navigate the digital world.",
                            "image_url"=> "https://www.banglalink.net/sites/default/files/Home-Banner-1920-X-870_0.jpg",
                            "thumb_image_url"=> "https://www.banglalink.net/sites/default/files/Home-Banner-1920-X-870_0.jpg",
                            "alt_text"=> "Internet offer image",
                            "action_btn_label"=> "Internet Offers",
                            "action_btn_url"=> "https://www.banglalink.net/offers",
                            "is_external_url"=> false
                        ]
                    ]
                ],
                [
                    "id"=> 1,
                    "title"=> "MOBILE RECHARGE & POSTPAID BILL PAYMENT",
                    "description"=> "",
                    "shortcode"=> "RechargeAndServices",
                    "data" => []
                ],
                [
                    "shortcode"=> "QuickLaunch",
                    "data" => $quickLaunch
                ],
                [
                    "id"=> 2,
                    "title"=> "Home page exple device",
                    "description"=> "",
                    "shortcode"=> "ExploreDevices",
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
                [
                    "id"=> 3,
                    "title"=> "Home page digital services slider",
                    "description"=> "",
                    "shortcode"=> "DigitalServices",
                    "data" => [
                        [
                            "title"=> "Banglaflix",
                            "description"=> "Mobile TV brings live TV &amp; Video on Demand (VOD) streaming on a mobile phone.",
                            "short_note"=> "Monthly ৳ 50",
                            "image_url"=> "https://www.banglalink.net/sites/default/files/Home-Banner-1920-X-870_0.jpg",
                            "alt_text"=> "Degital Service Banglaflix",
                            "external_apps_url1"=> "https:www.play.google.com/Banglaflix",
                            "external_apps_url2"=> "https://www.apple.com/ios/app-store/Banglaflix"
                        ],
                        [
                            "title"=> "Mobile Tv",
                            "description"=> "Mobile TV brings live TV &amp; Video on Demand (VOD) streaming on a mobile phone.",
                            "short_note"=> "Monthly ৳ 50",
                            "image_url"=> "https://www.banglalink.net/sites/default/files/Home-Banner-1920-X-870_0.jpg",
                            "alt_text"=> "Degital Service Banglaflix",
                            "apps_url1"=> "https:www.play.google.com/Banglaflix",
                            "apps_url2"=> "https://www.apple.com/ios/app-store/Banglaflix",
                        ],
                        [
                            "title"=> "Gaan Mela",
                            "description"=> "Mobile TV brings live TV &amp; Video on Demand (VOD) streaming on a mobile phone.",
                            "short_note"=> "Monthly ৳ 50",
                            "image_url"=> "https://www.banglalink.net/sites/default/files/Home-Banner-1920-X-870_0.jpg",
                            "alt_text"=> "Degital Service Banglaflix",
                            "external_apps_url1"=> "https:www.play.google.com/Banglaflix",
                            "external_apps_url2"=> "https://www.apple.com/ios/app-store/Banglaflix"
                        ],
                        [
                            "title"=> "Boi Ghar",
                            "description"=> "Mobile TV brings live TV &amp; Video on Demand (VOD) streaming on a mobile phone.",
                            "short_note"=> "Monthly ৳ 50",
                            "image_url"=> "https://www.banglalink.net/sites/default/files/Home-Banner-1920-X-870_0.jpg",
                            "alt_text"=> "Degital Service Banglaflix",
                            "apps_url1"=> "https:www.play.google.com/Banglaflix",
                            "apps_url2"=> "https://www.apple.com/ios/app-store/Banglaflix",
                        ],
                        [
                            "title"=> "Others",
                            "description"=> "Mobile TV brings live TV &amp; Video on Demand (VOD) streaming on a mobile phone.",
                            "short_note"=> "Monthly ৳ 50",
                            "image_url"=> "https://www.banglalink.net/sites/default/files/Home-Banner-1920-X-870_0.jpg",
                            "alt_text"=> "Degital Service Banglaflix",
                            "apps_url1"=> "https:www.play.google.com/Banglaflix",
                            "apps_url2"=> "https://www.apple.com/ios/app-store/Banglaflix",
                        ]
                    ]
                ],
                [
                    "id"=> 1,
                    "title"=> "Testimonial",
                    "description"=> "asdf sdfsdf sdf",
                    "shortcode"=> "Testimonial",
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
