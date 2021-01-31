<?php

namespace App\Services;

use App\Http\Resources\PartnerOfferResource;
use App\Models\AlSliderComponentType;
use App\Models\AlSliderImage;
use App\Models\BusinessOthers;
use App\Models\MetaTag;
use App\Models\ShortCode;
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

    /**
     * @var ImageFileViewerService
     */
    private $imageFileViewerService;

    protected $redis_ttl = 60 * 60 * 24;


    /**
     * HomeService constructor.
     * @param SliderRepository $sliderRepository
     * @param ProductService $productService
     * @param QuickLaunchService $quickLaunchService
     * @param EcareerService $ecarrerService
     * @param SalesAndServicesService $salesAndServicesService
     * @param ImageFileViewerService $imageFileViewerService
     */
    public function __construct(
        SliderRepository $sliderRepository,
        ProductService $productService,
        QuickLaunchService $quickLaunchService,
        EcareerService $ecarrerService,
        SalesAndServicesService $salesAndServicesService,
        ImageFileViewerService $imageFileViewerService
    ) {
        $this->productService = $productService;
        $this->sliderRepository = $sliderRepository;
        $this->quickLaunchService = $quickLaunchService;
        $this->ecarrerService = $ecarrerService;
        $this->salesAndServicesService = $salesAndServicesService;
        $this->imageFileViewerService = $imageFileViewerService;
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
        $keyData = config('filesystems.moduleType.AlSliderImage');
        foreach ($requests as $request) {
            $data = [];

            if ($component == "Corona") {
                $bnsModel = BusinessOthers::where('type', $request->id)->first();
                $data['details_id'] = $bnsModel->id;
                $data['url_slug'] = $bnsModel->url_slug;
            }

            $imgData = $this->imageFileViewerService->prepareImageData($request, $keyData);

            $data["id"] = $request->id ?? null;
            $data["slider_id"] = $request->slider_id ?? null;
            $data["title_en"] = $request->title_en ?? null;
            $data["title_bn"] = $request->title_bn ?? null;
            $data["start_date"] = $request->start_date ?? null;
            $data["end_date"] = $request->end_date ?? null;
            $data = array_merge($data, $imgData);
            $data["alt_text"] = $request->alt_text ?? null;
            $data["alt_text_bn"] = $request->alt_text_bn ?? null;
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

    public function getQuickLaunchData() {
        return [
            "component" => "QuickLaunch",
            "data" => $this->quickLaunchService->itemList('panel')
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

    public function getMultipleSliderData($id)
    {
        $slider = $this->sliderRepository->findOne($id);
        $this->bindDynamicValues($slider);

        $slider->component = AlSliderComponentType::find($slider->component_id)->slug;

        if ($id == 4) {
            $partnerOffers = DB::table('partner_offers as po')
                ->where('po.show_in_home', 1)
                ->where('po.is_active', 1)
                ->join('partners as p', 'po.partner_id', '=', 'p.id')
                ->join('partner_categories as pc', 'p.partner_category_id', '=', 'pc.id') // you may add more joins
                ->select('po.*', 'pc.name_en AS offer_type_en', 'pc.name_bn AS offer_type_bn', 'p.company_name_en', 'p.company_name_bn', 'p.company_logo')
                ->orderBy('po.display_order')
                ->get();

            $slider->data = PartnerOfferResource::collection($partnerOffers);
        } else {
            $products = $this->productService->trendingProduct();
            $slider->data = $products;
        }

        return $slider;
    }

    public function factoryComponent($type, $id)
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
                $data = $this->getMultipleSliderData($id);
                break;
            case "sales_service":
                $data = $this->getSalesServiceData();
                break;
            default:
                $data = "No suitable component found";
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
                $homePageData[] = $this->factoryComponent($component->component_type, $component->component_id);
            }
         Redis::setex('al_home_components', 3600, json_encode($homePageData));
           $value = Redis::get('al_home_components');
       } else {
            $value = "";
       }

        $data = [
            'metatags' => $metainfo,
            'components' => $value
        ];

        return $this->sendSuccessResponse($data, 'Home page components data');
    }
}
