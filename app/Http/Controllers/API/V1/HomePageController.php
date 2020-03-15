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
use App\Models\MetaTag;
use App\Services\ProductService;
use App\Services\QuickLaunchService;
use Illuminate\Database\QueryException;
use App\Http\Controllers\Controller;
use DB;
use Validator;
use App\Services\EcareerService;
use Symfony\Component\Debug\Exception\FatalThrowableError;


class HomePageController extends Controller
{

	/**
	 * @var ProductService
	 */
	private $productService;
	private $quickLaunchService;
	private $ecarrerService;

	/**
	 * HomePageController constructor.
	 * @param ProductService $productService
	 * @param QuickLaunchService $quickLaunchService
	 * @param EcareerService $ecarrerService
	 */
	public function __construct(
		ProductService $productService,
		QuickLaunchService $quickLaunchService,
		EcareerService $ecarrerService
	)
	{
		$this->productService = $productService;
		$this->quickLaunchService = $quickLaunchService;
		$this->ecarrerService = $ecarrerService;
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
				$data["mobile_view_img"] = ($request->mobile_view_img) ? config('filesystems.image_host_url') . $request->mobile_view_img : null;
				$data["alt_text"] = $request->alt_text ?? null;
				$data["display_order"] = $request->display_order ?? null;
				$data["is_active"] = $request->is_active ?? null;
				if ($request->other_attributes){
					foreach ($request->other_attributes as $key => $value) {
						$data[$key] = $value;
					}
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

	public function getSalesServiceData()
	{
		return  [
			"component"=> "SalesService",
			"data" => []
			// "data" => $salesServiceItems = $this->quickLaunchService->itemList('panel')
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
			case "sales_service":
				$data = $this->getSalesServiceData();
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


	/**
	 * Frontend dynamic route for seo tab
	 * @return [type] [description]
	 */
	public function frontendDynamicRoute(){

		$data = [];

		try{
			$parent_code = "ECareer";
			$parent_url = "/e-career";
			# eCarrer frontend route fro programs
			$ecarrer_data['code'] = $parent_code;
			$ecarrer_data['url'] = $parent_url;

			# eCarrer children data

			# programs routes
			$programs_slug = $this->ecarrerService->getProgramsAllTabTitle('life_at_bl_topbanner', 'programs', true);

			$extra_slug_data = [$programs_slug];

			$programs_child_data = $this->ecarrerService->getProgramsAllTabTitle('programs_top_tab_title');

			$programs_child_data_results = $this->formatDynamicRoute($programs_child_data, $parent_code, $parent_url, $extra_slug_data);

			# life at banglalink all top banner slug
			$top_banner_slug = $this->ecarrerService->getProgramsAllTabTitle('life_at_bl_topbanner');

			$top_banner_data_results = $this->formatDynamicRoute($top_banner_slug, $parent_code, $parent_url, null);

			if( !empty($top_banner_data_results) ){

				if( !empty($programs_child_data_results) ){
					$child_data = array_merge($top_banner_data_results, $programs_child_data_results );
				}
				else{
					$child_data = $top_banner_data_results;
				}

			}
			else{
				$child_data = null;
			}



			$ecarrer_data['children'] = $child_data;


			$data[] = $ecarrer_data;

			return response()->success($data, "Data Success");
		}
		catch(\Exception $e){
		 return response()->error('Route not found.', $e->getMessage());
		}
		catch(FatalThrowableError $e) {
		   return response()->error('Internal server error.', $e->getMessage());
		}



	}

	/**
	 * [formatDynamicRoute description]
	 * @param $data
	 * @param $parent_code
	 * @param $parent_url
	 * @param null $extra_slug_data
	 * @return array|null [type]                  [description]
	 */
	private function formatDynamicRoute($data, $parent_code, $parent_url, $extra_slug_data = null){

	  try{
		 $results = null;
		 if( is_array($data) ){

			if( !empty($extra_slug_data) && is_array($extra_slug_data) ){
			   $additional_url_slug = implode('/', $extra_slug_data);
			}
			else{
			   $additional_url_slug = null;
			}

			foreach ($data as $value) {

			   $sub_data = [];

			   $sub_data['code'] = $parent_code;

			   if( !empty($additional_url_slug) ){
				  $sub_data['url'] = $parent_url .'/'. $additional_url_slug .'/'. $value['slug'];
			   }
			   else{
				  $sub_data['url'] = $parent_url .'/'. $value['slug'];
			   }

			   $results[] = $sub_data;

			}


		 }

		 return $results;
	  }
	  catch(\Exception $e){
		 return response()->error('Internal server error.', $e->getMessage());
	  }
	  catch(FatalThrowableError $e) {
		 return response()->error('Internal server error.', $e->getMessage());
	  }





	}


}
