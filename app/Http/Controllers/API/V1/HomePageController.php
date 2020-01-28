<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Resources\PartnerOfferResource;
use App\Http\Resources\QuickLaunchResource;
use App\Http\Resources\SliderImageResource;
use App\Models\QuickLaunch;
use App\Models\QuickLaunchItem;
use App\Models\AlSlider;
use App\Models\AlSliderComponentType;
use App\Models\AlSliderImage;
use App\Models\ShortCode;
use App\Models\PartnerOffer;
use App\Models\Product;
use App\Models\MetaTag;
use App\Services\ProductService;
use App\Services\QuickLaunchService;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Validator;


class HomePageController extends Controller
{

    /**
     * @var ProductService
     */
    private $productService;
    private $quickLaunchService;

    /**
     * HomePageController constructor.
     * @param ProductService $productService
     * @param QuickLaunchService $quickLaunchService
     */
    public function __construct(
        ProductService $productService,
        QuickLaunchService $quickLaunchService
    )
    {
        $this->productService = $productService;
        $this->quickLaunchService = $quickLaunchService;
    }

    // In PHP, By default objects are passed as reference copy to a new Object.
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

    public function getSliderData($id){

        $slider = AlSlider::find($id);
        $component = AlSliderComponentType::find($slider->component_id)->slug;

        $limit = ( $component == 'Testimonial' ) ? 5 : false;

        $query = AlSliderImage::where('slider_id',$id)
                                ->where('is_active',1)
                                ->orderBy('display_order');

        $slider_images =  $limit ? $query->limit($limit)->get() : $query->get();

        $slider_images = $this->makeResource($slider_images);

        $this->bindDynamicValues($slider);

        $slider->component = $component;
        $slider->data = $slider_images;
        return $slider;
    }

    public function makeResource($requests) {
        {
            $result = [];
            foreach ($requests as $request) {
                $data = [];
                $data["id"] = $request->id ?? null;
                $data["slider_id"] = $request->slider_id ?? null;
                $data["title_en"] = $request->title_en ?? null;
                $data["title_bn"] = $request->title_bn ?? null;
                $data["start_date"] = $request->start_date ?? null;
                $data["end_date"] = $request->end_date ?? null;
                $data["image_url"] = config('filesystems.image_host_url') . $request->image_url;
                $data["alt_text"] = $request->alt_text ?? null;
                $data["display_order"] = $request->display_order ?? null;
                $data["is_active"] = $request->is_active ?? null;
                foreach ($request->other_attributes as $key => $value) {
                    $data[$key] = $value;
                }

                array_push($result, (object)$data);
            }
            return  $result;
        }
    }

    public function getQuickLaunchData()
    {
        return  [
            "component"=> "QuickLaunch",
            "data" => $quickLaunchItems = $this->quickLaunchService->itemList('panel')
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


    public function getMultipleSliderData($id)
    {
        $slider = AlSlider::find($id);
        $this->bindDynamicValues($slider);

        $slider->component = AlSliderComponentType::find($slider->component_id)->slug;



        if($id == 4){
            $partnerOffers =  DB::table('partner_offers as po')
                ->where('po.show_in_home',1)
                ->where('po.is_active',1)
                ->join('partners as p', 'po.partner_id', '=', 'p.id')
                ->join('partner_categories as pc', 'p.partner_category_id', '=', 'pc.id') // you may add more joins
                ->select('po.*', 'pc.name_en AS offer_type_en', 'pc.name_bn AS offer_type_bn', 'p.company_name_en','p.company_name_bn','p.company_logo')
                ->orderBy('po.display_order')
                ->get();

            $slider->data = PartnerOfferResource::collection($partnerOffers);

        }else {
            $products = $this->productService->trendingProduct();
            $slider->data = $products;
        }

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
                $data = $this->getMultipleSliderData($id);
                break;
            default:
                $data = "No suitable component found";
        }

        return $data;
    }


    public function getHomePageData()
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

    /**
     *  Macro & mixin sample output for
     */

    public function macro(){

        $input = request()->all();

        $validator = Validator::make($input, [
            'name' => 'required',
            'detail' => 'required'
        ]);


        if($validator->fails()){
            return response()->error('Validation Error.', $validator->errors());
        }

        $result  = [
            ['id' => 1],
            ['id' => 2]
        ];

        return response()->success($result, "Data Success");
    }
}
