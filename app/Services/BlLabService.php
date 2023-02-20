<?php

namespace App\Services;


use App\Models\AlFaq;
use App\Models\AlSliderComponentType;
use App\Models\AlSliderImage;

use App\Models\BusinessOthers;

use App\Models\MetaTag;

use App\Models\ShortCode;
use App\Repositories\MediaPressNewsEventRepository;
use App\Repositories\SliderRepository;
use Illuminate\Support\Facades\Redis;

/**
 * Class BannerService
 * @package App\Services
 */
class BlLabService extends ApiBaseService
{
    /**
     * @var ProductService
     */
    private $sliderRepository;





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
     *
     */
    public function __construct(
        SliderRepository $sliderRepository
    ) {
        $this->sliderRepository = $sliderRepository;

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
                $data["icon_image"] = $request->icon_image ?? null;
                $data["icon_alt_text_en"] = $request->icon_alt_text_en ?? null;
                $data["icon_alt_text_bn"] = $request->icon_alt_text_bn ?? null;



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





    public function factoryComponent($type, $id, $component, $params = []) {

        // $customerInfo = $params['customerInfo'] ?? '';
        // $customerAvailableProducts = $params['customerAvailableProducts'] ?? [];

        $data = null;
        switch ($type) {
            case "slider_single":
                $data = $this->getSliderData($id,$component);
                break;
            case "besic":
                $data = $this->getBasicData($component);
                break;
            case "faq":
                $data = $this->getFaqData($component);
                break;
            default:
                $data = "No suitable component found";
        }

        return $data;
    }


    public function getBasicData($component)
    {
        $data = $this->commonRes($component);
        return $data;
    }
    public function getFaqData($component)
    {
        $data = $this->commonRes($component);
        $data['data'] = AlFaq::where('slug', 'bl_lab_faq')->get();
        //$data['data'] = [];
        //dd($data);
        return $data;
    }

    private function commonRes($component){
        $data = Shortcode::findOrFail($component->id);
        if ($component->other_attributes) {
            foreach ($component->other_attributes as $key => $value) {
                $data[$key] = $value;
            }
        }
        return $data;
    }
    public function getComponents($request)
    {

        $componentList = ShortCode::where('page_id', 3)
            ->where('is_active', 1)
            ->orderBy('sequence', 'ASC')
            ->get();
        $metainfo = MetaTag::where('page_id', 1)
            ->first()->toArray();
        if (!$value = Redis::get('bl_lab_components')){
            $homePageData = [];
            foreach ($componentList as $component) {
                $homePageData[] = $this->factoryComponent($component->component_type, $component->component_id, $component, ['customerInfo' => '', 'customerAvailableProducts' => '']);
            }
            $value = json_encode($homePageData);
            //Redis::setex('al_home_components', 3600, json_encode($homePageData));
            //$value = Redis::get('al_home_components');
        } else {
            //$value = Redis::get('al_home_components');
        }
        $data = [
            //'metatags' => $metainfo,
            'components' => json_decode($value)
        ];

        return $this->sendSuccessResponse($data, 'Bl Lab page components data');
    }
}
