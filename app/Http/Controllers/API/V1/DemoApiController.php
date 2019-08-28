<?php
 
namespace App\Http\Controllers;
 
use App\Models\Car;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
 
class DemoApiController extends Controller{

	
    public function menu()
    {

        // Apps & Services, Business, Loyalty,eShop
        $menu = [
            [
                'id' => 1,
                'title' => 'Home',
                'url' => 'http://banglalink.net',
                'display_order' => 1,
                'parent' => 0,
                'chield_menus' => []
            ],
            [
                'id' => 2,
                'title' => 'Offers',
                'url' => 'http://banglalink.net/offers',
                'display_order' => 2,
                'parent' => 0,
                'child_menus' => [
                    [
                        'id' => 6,
                        'title' => 'Prepaid',
                        'url' => 'http://banglalink.net/prepaid',
                        'display_order' => 1,
                        'parent' => 2
                    ],[
                        'id' => 7,
                        'title' => 'Postpaid',
                        'url' => 'http://banglalink.net/postpaid',
                        'display_order' => 2,
                        'parent' => 2
                    ],[
                        'id' => 8,
                        'title' => 'Propaid',
                        'url' => 'http://banglalink.net/propaid',
                        'display_order' => 3,
                        'parent' => 2
                    ]
                ] 
            ],[
                'id' => 3,
                'title' => 'Apps & Services',
                'url' => 'http://banglalink.net/app-service',
                'display_order' => 3,
                'parent' => 0,
            ],
            [
                'id' => 4,
                'title' => 'Business',
                'url' => 'http://banglalink.net/business',
                'display_order' => 4,
                'parent' => 0,
            ],
            [
                'id' => 5,
                'title' => 'eShop',
                'url' => 'http://banglalink.net/eshop',
                'display_order' => 5,
                'parent' => 0,
            ]
        ];
        
    	return response()->json($menu); 
    }

	public function slider(){
        $sliders = [
            'hero' => [
                'id' => 1,
                'title' => 'Home page main slider',
                'description' => '',
                'shortcode' => 'hero',
                'images' => [
                    [
                        'title' =>  'Extra internet for all Banglalink users',
                        'description' => 'Banglalink is one of the leading digital communications service providers in Bangladesh working to unlock new opportunities for its customers as they navigate the digital world.',
                        'image_url' => 'https://www.banglalink.net/sites/default/files/Home-Banner-1920-X-870_0.jpg',
                        'alt_text' => 'Internet offer image',
                        'url_btn_label' =>  'Internet Offers',
                        'external_url' => 'https://www.banglalink.net/offers'
                    ],
                    [
                        'title' =>  'Extra internet for all Banglalink users',
                        'description' => 'Banglalink is one of the leading digital communications service providers in Bangladesh working to unlock new opportunities for its customers as they navigate the digital world.',
                        'image_url' => 'https://www.banglalink.net/sites/default/files/Home-Banner-1920-X-870_0.jpg',
                        'alt_text' => 'Internet offer image',
                        'url_btn_label' => 'Internet Offers',
                        'external_url' => 'https://www.banglalink.net/offers'
                    ] 
                ]
            ],
            'explore' => [
                'id' => 1,
                'title' => 'Home page exple device',
                'description' => '',
                'shortcode' => 'explore',
                'images' => [
                    [
                        'title' =>  'Find the best deals from our eshop and enjoy exclusive offers!',
                        'description' => 'Brilliant. In every way.',
                        'image_url' => 'https://www.banglalink.net/sites/default/files/Home-Banner-1920-X-870_0.jpg',
                        'alt_text' => 'Explore device : samsung',
                        'url_btn_label' => 'Show now',
                        'external_url' => 'https://www.banglalink.net/en/business/business-solutions/m-connex'
                    ],
                    [
                        'title' =>  'Find the best deals from our eshop and enjoy exclusive offers!',
                        'description' => 'Brilliant. In every way.',
                        'image_url' => 'https://www.banglalink.net/sites/default/files/Home-Banner-1920-X-870_0.jpg',
                        'alt_text' => 'Explore device : samsung',
                        'url_btn_label' => 'Show now',
                        'external_url' => 'https://www.banglalink.net/en/business/business-solutions/m-connex'
                    ] 
                ]
            ],
            'service' => [
                'id' => 1,
                'title' => 'Home page digital services slider',
                'description' => '',
                'shortcode' => 'service',
                'images' => [
                    [
                        'title' =>  'Banglaflix',
                        'description' => 'Mobile TV brings live TV &amp; Video on Demand (VOD) streaming on a mobile phone.',
                        'image_url' => 'https://www.banglalink.net/sites/default/files/Home-Banner-1920-X-870_0.jpg',
                        'alt_text' => 'Degital Service Banglaflix',
                        'url_btn_label' => 'Banglaflix',
                        'url' => '',
                        'Other note' => 'Monthly ৳ 50'
                    ],
                    [
                        'title' =>  'Mobile TV',
                        'description' => 'Mobile TV brings live TV &amp; Video on Demand (VOD) streaming on a mobile phone.',
                        'image_url' => 'https://www.banglalink.net/sites/default/files/Home-Banner-1920-X-870_0.jpg',
                        'alt_text' => 'Degital service Mobile TV',
                        'url_btn_label' => 'Banglaflix',
                        'url' => '',
                        'Other note' => 'Monthly ৳ 50'
                    ],[
                        'title' =>  'Gaan Mela',
                        'description' => 'Mobile TV brings live TV &amp; Video on Demand (VOD) streaming on a mobile phone.',
                        'image_url' => 'https://www.banglalink.net/sites/default/files/Home-Banner-1920-X-870_0.jpg',
                        'alt_text' => 'Degital Service Gaan Mela',
                        'url_btn_label' => 'Banglaflix',
                        'url' => '',
                        'Other note' => 'Monthly ৳ 50'
                    ],
                    [
                        'title' =>  'Boi Ghor',
                        'description' => 'Mobile TV brings live TV &amp; Video on Demand (VOD) streaming on a mobile phone.',
                        'image_url' => 'https://www.banglalink.net/sites/default/files/Home-Banner-1920-X-870_0.jpg',
                        'alt_text' => 'Degital service Boi Ghor',
                        'url_btn_label' => 'Banglaflix',
                        'url' => '',
                        'Other note' => 'Monthly ৳ 50'
                    ]
                ]
            ],
            'client' => [
                'id' => 1,
                'title' => 'Testimonial',
                'description' => 'asdf sdfsdf sdf',
                'shortcode' => 'client',
                'images' => [
                    [
                        'title' =>  'Shahriar Ahmed',
                        'description' => 'Banglalink provide the fastest internet throughout the country, I never get the best experience except using Banlalink. It’s awesome service ever, I’ll always use Banglalink.',
                        'image_url' => 'https://www.banglalink.net/sites/default/files/Home-Banner-1920-X-870_0.jpg',
                        'alt_text' => 'Banglalink clients',
                        'url_btn_label' => 'sadfsd fsdfdfs',
                        'url' => 'https://www.banglalink.net/en/business/business-solutions/m-connex',
                        'Other note' => 'Studiomaqs'
                    ],
                    [
                        'title' =>  'Shahriar Ahmed',
                        'description' => 'Banglalink provide the fastest internet throughout the country, I never get the best experience except using Banlalink. It’s awesome service ever, I’ll always use Banglalink.',
                        'image_url' => 'https://www.banglalink.net/sites/default/files/Home-Banner-1920-X-870_0.jpg',
                        'alt_text' => 'Banglalink clients',
                        'url_btn_label' => '',
                        'url' => '',
                        'Other note' => 'Studiomaqs'
                    ],[
                        'title' =>  'Shahriar Ahmed',
                        'description' => 'Banglalink provide the fastest internet throughout the country, I never get the best experience except using Banlalink. It’s awesome service ever, I’ll always use Banglalink.',
                        'image_url' => 'https://www.banglalink.net/sites/default/files/Home-Banner-1920-X-870_0.jpg',
                        'alt_text' => 'Banglalink clients',
                        'url_btn_label' => 'sadfsd fsdfdfs',
                        'url' => 'https://www.banglalink.net/en/business/business-solutions/m-connex',
                        'Other note' => 'Studiomaqs'
                    ],
                    [
                        'title' =>  'Shahriar Ahmed',
                        'description' => 'Banglalink provide the fastest internet throughout the country, I never get the best experience except using Banlalink. It’s awesome service ever, I’ll always use Banglalink.',
                        'image_url' => 'https://www.banglalink.net/sites/default/files/Home-Banner-1920-X-870_0.jpg',
                        'alt_text' => 'Banglalink clients',
                        'url_btn_label' => '',
                        'url' => '',
                        'Other note' => 'Studiomaqs'
                    ] 
                ]
            ],
        ];
        
    	return response()->json($sliders); 
    }
    

    public function footer()
    {

        // Apps & Services, Business, Loyalty,eShop
        $footer_menu = [
            [
                'id' => 1,
                'title' => 'About',
                'url' => 'http://banglalink.net/about',
                'display_order' => 1,
                'parent' => 0,
                'child_menus' => [
                    [
                        'id' => 6,
                        'title' => 'About Banglalink',
                        'url' => 'http://banglalink.net/about-banglalink',
                        'display_order' => 1,
                        'parent' => 1
                    ],[
                        'id' => 7,
                        'title' => 'About Veon',
                        'url' => 'http://banglalink.net/postpaid',
                        'display_order' => 2,
                        'parent' => 1
                    ],[
                        'id' => 8,
                        'title' => 'Suppliers and Partners',
                        'url' => 'http://banglalink.net/suppliers-and-partners',
                        'display_order' => 3,
                        'parent' => 1
                    ]
                ] 
            ],
            [
                'id' => 2,
                'title' => 'Offer',
                'url' => 'http://banglalink.net/offers',
                'display_order' => 2,
                'parent' => 0,
                'child_menus' => [
                    [
                        'id' => 6,
                        'title' => 'Prepaid',
                        'url' => 'http://banglalink.net/prepaid',
                        'display_order' => 1,
                        'parent' => 2
                    ],[
                        'id' => 7,
                        'title' => 'Postpaid',
                        'url' => 'http://banglalink.net/postpaid',
                        'display_order' => 2,
                        'parent' => 2
                    ],[
                        'id' => 8,
                        'title' => 'Roaming',
                        'url' => 'http://banglalink.net/roaming',
                        'display_order' => 3,
                        'parent' => 2
                    ]
                ] 
            ],[
                'id' => 3,
                'title' => 'Apps & Services',
                'url' => 'http://banglalink.net/app-service',
                'display_order' => 3,
                'parent' => 0,
            ],
            [
                'id' => 4,
                'title' => 'Business',
                'url' => 'http://banglalink.net/business',
                'display_order' => 4,
                'parent' => 0,
            ],
            [
                'id' => 5,
                'title' => 'Useful Links',
                'url' => 'http://banglalink.net/useful-links',
                'display_order' => 5,
                'parent' => 0,
            ]
        ];
        
    	return response()->json($menu); 
    }

}