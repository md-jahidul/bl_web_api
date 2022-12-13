<?php

namespace App\Services;

use App\Http\Resources\BlogResource;
use App\Http\Resources\OclaResource;
use App\Http\Resources\PartnerOfferResource;
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
        BusinessTypeService $businessTypeService
    ) {
        $this->productService = $productService;
        $this->sliderRepository = $sliderRepository;
        $this->quickLaunchService = $quickLaunchService;
        $this->ecarrerService = $ecarrerService;
        $this->salesAndServicesService = $salesAndServicesService;
        $this->aboutUsRepository = $aboutUsRepository;
        $this->partnerOfferService = $partnerOfferService;
        $this->businessTypeService = $businessTypeService;
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

    public function getSliderData($id) {
        $slider = $this->sliderRepository->findOne($id);
//        dd($slider);

        $component = AlSliderComponentType::find($slider->component_id)->slug;

        $limit = ( $component == 'Testimonial' ) ? 5 : false;

        $query = AlSliderImage::where('slider_id', $id)
            ->where('is_active', 1)
            ->orderBy('display_order');

        $slider_images = $limit ? $query->limit($limit)->get() : $query->get();

        $slider_images = $this->makeResource($slider_images, $component);

        $this->bindDynamicValues($slider);

        $slider->component = $component;
        $slider->data = $slider_images;
        return $slider;
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


            $data["id"] = $request->id ?? null;
            $data["slider_id"] = $request->slider_id ?? null;
            $data["title_en"] = $request->title_en ?? null;
            $data["title_bn"] = $request->title_bn ?? null;
            $data["description_bn"] = $request->description_bn ?? null;
            $data["description_en"] = $request->description_en ?? null;

            $data["start_date"] = $request->start_date ?? null;
            $data["end_date"] = $request->end_date ?? null;
            $data["image_url"] = config('filesystems.image_host_url') . $request->image_url;
            $data["mobile_view_img"] = ($request->mobile_view_img) ? config('filesystems.image_host_url') . $request->mobile_view_img : null;
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
        return $result;
    }
    }

    public function getQuickLaunchData($component) {
        return [
            "component" => "QuickLaunch",
            "title_en" => $component->title_en ?? null,
            "title_bn" => $component->title_bn ?? null,
            "description_en" => $component->description_en ?? null,
            "deccription_bn" => $component->deccription_bn ?? null,
            "data" => $quickLaunchItems = $this->quickLaunchService->itemList('panel')
        ];
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

    public function getMultipleSliderData($id) {
//        $slider = AlSlider::find($id);
        $slider = $this->sliderRepository->findOne($id);
        $this->bindDynamicValues($slider);

        $slider->component = AlSliderComponentType::find($slider->component_id)->slug;

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
        else if($id == 13){
            $slider->data =  $this->businessTypeService->getBusinessTypeInfo();

        }
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
                $data = $this->getSliderData($id);
                break;
            case "recharge":
                $data = $this->getRechargeData();
                break;
            case "quicklaunch":
                $data = $this->getQuickLaunchData($component);
                break;
            case "slider_multiple":
                $data = $this->getMultipleSliderData($id);
                break;
            case "sales_service":
                $data = $this->getSalesServiceData();
                break;
            case "ocla":
                $data = $this->getOclaData($component);
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
            case "fast_forward":
                $data = $this->getFastForwardData($component);
                break;
            default:
                $data = "No suitable component found";
        }

        return $data;
    }


    public function getOclaData($component){
        $data = $this->dummyRes($component,'Ocla');
        $data['data'] = OclaResource::collection(Ocla::get());
        return $data;
    }

    public function getFastForwardData($component){
        $data = $this->dummyRes($component,'Fast Forward');
        $data['data'] = [];
        return $data;
    }

    public function getCareerData($component){
        $data = $this->dummyRes($component,'Carrer');
        $data['data'] = [];
        return $data;
    }

    public function getBlogData($component){
        $data = $this->dummyRes($component,'Blog');
        $data['data'] = BlogResource::collection(Blog::get());
        return $data;
    }

    public function getAboutData($component){
        $data = $this->dummyRes($component,'About');
        $data['data'] =  $this->aboutUsRepository->getAboutBanglalink();
        return $data;
    }

    public function getMemoryData($component){
        $data = $this->dummyRes($component,'Memory');
        $data['data'] = MediaTvcVideo::get();
        return $data;
    }

    public function getSuperAppLandingData($component){
        $data = $this->dummyRes($component,'Super App');
        $data['data'] = [];
        return $data;
    }

    public function getMapViewData($component){
        $data = $this->dummyRes($component,'Map View');
        $data['data'] = [];
        return $data;
    }

    private function dummyRes($component,$dummyName){
        return collect([
            "component" => $dummyName,
            "title_en" => $component->title_en ?? null,
            "title_bn" => $component->title_bn ?? null,
            "description_en" => $component->description_en ?? null,
            "deccription_bn" => $component->deccription_bn ?? null,
            "link_en" => $component->link_en ?? null,
            "link_bn" => $component->link_bn ?? null,
            "label_bn" => $component->label_bn ?? null,
            "label_en" => $component->label_bn ?? null,
            "is_label_active" => $component->is_label_active ?? null,
            "other_attributes" => $component->other_attributes ?? null ,
            //"data" => $quickLaunchItems = $this->oclaService->itemList('panel')
        ]);
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
