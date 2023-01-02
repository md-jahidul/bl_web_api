<?php

namespace App\Services;

use App\Http\Resources\AboutUsBanglalinkResource;
use App\Http\Resources\BlogResource;
use App\Http\Resources\OclaResource;
use App\Http\Resources\PartnerOfferResource;
use App\Http\Resources\ShortcodeResource;
use App\Models\AboutPage;
use App\Models\AlSliderComponentType;
use App\Models\AlSliderImage;
use App\Models\Blog;
use App\Models\BusinessOthers;
use App\Models\MediaTvcVideo;
use App\Models\MetaTag;
use App\Models\ShortCode;
use App\Models\Ocla;
use App\Repositories\AboutUsRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\MediaPressNewsEventRepository;
use App\Repositories\SliderRepository;
use App\Services\Banglalink\CustomerPackageService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

/**
 * Class BannerService
 * @package App\Services
 */
class HomeService extends ApiBaseService
{
    /**
     * @var ProductService
     */
    private $sliderRepository;
    private $productService;
    private $quickLaunchService;
    private $ecarrerService;
    private $salesAndServicesService;
    private $aboutUsRepository;
    private $businessTypeService;





    protected $redis_ttl = 60 * 60 * 24;
    /**
     * @var PartnerOfferService
     */
    private $partnerOfferService;
    /**
     * @var MediaPressNewsEventRepository
     */
    private $mediaPressNewsEventRepository;

    /**
     * HomeService constructor.
     * @param SliderRepository $sliderRepository
     * @param ProductService $productService
     * @param QuickLaunchService $quickLaunchService
     * @param EcareerService $ecarrerService
     * @param SalesAndServicesService $salesAndServicesService
     * @param AboutUsRepository $aboutUsRepository
     * @param BusinessTypeService $businessTypeService
     *
     */
    public function __construct(
        SliderRepository $sliderRepository,
        ProductService $productService,
        QuickLaunchService $quickLaunchService,
        EcareerService $ecarrerService,
        SalesAndServicesService $salesAndServicesService,
        AboutUsRepository $aboutUsRepository,
        PartnerOfferService $partnerOfferService,
        BusinessTypeService $businessTypeService,
        MediaPressNewsEventRepository $mediaPressNewsEventRepository
    ) {
        $this->productService = $productService;
        $this->sliderRepository = $sliderRepository;
        $this->quickLaunchService = $quickLaunchService;
        $this->ecarrerService = $ecarrerService;
        $this->salesAndServicesService = $salesAndServicesService;
        $this->aboutUsRepository = $aboutUsRepository;
        $this->partnerOfferService = $partnerOfferService;
        $this->businessTypeService = $businessTypeService;
        $this->mediaPressNewsEventRepository = $mediaPressNewsEventRepository;
    }


    // In PHP, By default objects are passed as reference copy to a new Object.
    public function bindDynamicValues($obj, $json_data = 'other_attributes') {
        if (!empty($obj->{ $json_data })) {
            foreach ($obj->{ $json_data } as $key => $value) {
                $obj->{$key} = $value;
            }
        }
        unset($obj->{ $json_data });
    }

    public function getSliderData($id,$shortCode) {
        //dd($shortCode);
        $slider = $this->sliderRepository->findOne($id);
//        dd($slider);

        $component = AlSliderComponentType::find($slider->component_id)->slug;

        $limit = ( $component == 'Testimonial' ) ? 5 : false;

        $query = AlSliderImage::where('slider_id', $id)
            ->where('is_active', 1)
            ->orderBy('display_order');

        $slider_images = $limit ? $query->limit($limit)->get() : $query->get();

        $slider_images = $this->makeResource($slider_images, $component);

        //$this->bindDynamicValues($slider);

        $this->bindDynamicValues($shortCode);
        $slider->component = $component;
        //$slider->data = $slider_images;
        $shortCode->data = $slider_images;
        //return $slider;
        return $shortCode;
    }

    public function makeResource($requests, $component) { {
        $result = [];

        foreach ($requests as $request) {
            $data = [];

            if ($component == "Corona") {
                $bnsModel = BusinessOthers::where('type', $request->id)->first();
                $data['details_id'] = $bnsModel->id;
                $data['url_slug'] = $bnsModel->url_slug;
                $data['url_slug_bn'] = $bnsModel->url_slug_bn;
            }
            // if ($component == "Ocla") {
            //     echo $component;
            //     $result =  new ShortcodeResource($component);
            // }
            else{

                $data["id"] = $request->id ?? null;
                $data["slider_id"] = $request->slider_id ?? null;
                $data["title_en"] = $request->title_en ?? null;
                $data["title_bn"] = $request->title_bn ?? null;
                $data["description_bn"] = $request->description_bn ?? null;
                $data["description_en"] = $request->description_en ?? null;

                $data["start_date"] = $request->start_date ?? null;
                $data["end_date"] = $request->end_date ?? null;
                $data["image_url"] = $request->image_url;
                $data["mobile_view_img"] = ($request->mobile_view_img) ? $request->mobile_view_img : null;
                $data["alt_text"] = $request->alt_text ?? null;
                $data["display_order"] = $request->display_order ?? null;
                $data["is_active"] = $request->is_active ?? null;



                if ($request->other_attributes) {
                    foreach ($request->other_attributes as $key => $value) {
                        $data[$key] = $value;
                    }
                }

                array_push($result, (object) $data);
            }
        }
        return $result;
    }
    }

    public function getQuickLaunchData($component) {
        $component = Shortcode::findOrFail($component->id);
        if ($component->other_attributes) {
            foreach ($component->other_attributes as $key => $value) {
                $component[$key] = $value;
            }
        }
        $component['data'] = $this->quickLaunchService->itemList('panel');
        return $component;
    }

    public function getSalesServiceData() {
        $results = $this->salesAndServicesService->itemHeader();
        $results['data'] = $this->salesAndServicesService->itemList();
        return $results;
    }

    public function getRechargeData() {
        return [
            "id" => 1,
            "title" => "MOBILE RECHARGE & POSTPAID BILL PAYMENT",
            "description" => "",
            "component" => "RechargeAndServices",
            "data" => []
        ];
    }

    public function getMultipleSliderData($id,$shortCode) {
//        $slider = AlSlider::find($id);
        //$slider = $this->sliderRepository->findOne($id);
        //$this->bindDynamicValues($slider);
        $slider = $shortCode;
        $this->bindDynamicValues($shortCode);

        //$slider->component = AlSliderComponentType::find($slider->component_id)->slug;
        //$slider = $shortCode;
        if ($id == 4) {
//            $partnerOffers = DB::table('partner_offers as po')
//                ->where('po.show_in_home', 1)
//                ->where('po.is_active', 1)
//                ->join('partners as p', 'po.partner_id', '=', 'p.id')
//                ->join('partner_categories as pc', 'p.partner_category_id', '=', 'pc.id') // you may add more joins
//                ->select('po.*', 'pc.name_en AS offer_type_en', 'pc.name_bn AS offer_type_bn', 'p.company_name_en', 'p.company_name_bn', 'p.company_logo')
//                ->orderBy('po.display_order')
//                ->get();
//            $slider->data = PartnerOfferResource::collection($partnerOffers);
//            dd($this->partnerOfferService->tierOffers(true));
            $slider->data = $this->partnerOfferService->tierOffers($showInHome = true);
        }
        // else if($id == 13){
        //     //$slider = $shortCode;
        //     //$slider->data =  $this->businessTypeService->getBusinessTypeInfo();

        // }
        else {
            $products = $this->productService->trendingProduct();
            $slider->data = $products;
        }

        return $slider;
    }

    public function factoryComponent($type, $id, $component) {

        $data = null;
        switch ($type) {
            case "slider_single":
                $data = $this->getSliderData($id,$component);
                break;
            case "recharge":
                $data = $this->getRechargeData();
                break;
            case "quicklaunch":
                $data = $this->getQuickLaunchData($component);
                break;
            case "slider_multiple":
                $data = $this->getMultipleSliderData($id,$component);
                break;
            case "sales_service":
                $data = $this->getSalesServiceData();
                break;
            case "map_view":
                $data = $this->getMapViewData($component);
                break;
            case "memory":
                $data = $this->getMemoryData($component);
                break;
            case "about":
                $data = $this->getAboutData($component);
                break;
            case "super_app":
                $data = $this->getSuperAppLandingData($component);
                break;
            case "blog":
                $data = $this->getBlogData($component);
                break;
            case "career":
                $data = $this->getCareerData($component);
                break;
            case "business":
                $data = $this->getBusinessData($component);
                break;
            case "fast_forward":
                $data = $this->getFastForwardData($component);
                break;
            default:
                $data = "No suitable component found";
        }

        return $data;
    }


    // public function getOclaData($component){
    //     $data = $this->dummyRes($component);
    //     $data['data'] = OclaResource::collection(Ocla::get());
    //     return $data;
    // }

    public function getFastForwardData($component){
        $data = $this->dummyRes($component);
        return $data;
    }
    public function getBusinessData($component){
        $data = $this->dummyRes($component);
        $data['data'] = $this->businessTypeService->getBusinessTypeInfo();
        return $data;
    }

    public function getCareerData($component){
        $data = $this->dummyRes($component);
        $data['data'] = [];
        return $data;
    }

    public function getBlogData($component){
        $data = $this->dummyRes($component);

        $blogPostsForHome = $this->mediaPressNewsEventRepository->findByProperties(['status' => 1, 'reference_type' => 'blog', 'show_in_home' => 1], [
            'title_en', 'title_bn', 'short_details_en', 'short_details_bn', 'thumbnail_image', 'date', 'read_time'
        ]);
        $data['data'] = BlogResource::collection($blogPostsForHome);
        return $data;
    }

    public function getAboutData($component){
        $data = $this->dummyRes($component);
        $data['data'] =  AboutUsBanglalinkResource::collection($this->aboutUsRepository->getAboutBanglalink());
        return $data;
    }

    public function getMemoryData($component){
        $data = $this->dummyRes($component);
        $data['data'] = MediaTvcVideo::where('status',1)->get();
        return $data;
    }

    public function getSuperAppLandingData($component){
        $data = $this->dummyRes($component);
        $data['data'] = [];
        return $data;
    }

    public function getMapViewData($component){
        $data = $this->dummyRes($component);
        $data['data'] = [];
        return $data;
    }

    private function dummyRes($component){
        $data = Shortcode::findOrFail($component->id);
        if ($component->other_attributes) {
            foreach ($component->other_attributes as $key => $value) {
                $data[$key] = $value;
            }
        }
        return $data;
    }
    public function getComponents()
    {
        $componentList = ShortCode::where('page_id', 1)
            ->where('is_active', 1)
            ->orderBy('sequence', 'ASC')
            ->get();
        $metainfo = MetaTag::where('page_id', 1)
            ->first()->toArray();
        if (!$value = Redis::get('al_home_components')){
            $homePageData = [];
            foreach ($componentList as $component) {
                // if($component->id === 19){
                //     continue;
                // }
                $homePageData[] = $this->factoryComponent($component->component_type, $component->component_id, $component);
            }
            $value = json_encode($homePageData);
            //Redis::setex('al_home_components', 3600, json_encode($homePageData));
            //$value = Redis::get('al_home_components');
        } else {
            //$value = Redis::get('al_home_components');
        }
        $data = [
            'metatags' => $metainfo,
            'components' => json_decode($value)
        ];

        return $this->sendSuccessResponse($data, 'Home page components data');
    }
}
