<?php

namespace App\Services;

//use App\Repositories\AppServiceProductegoryRepository;

use App\Repositories\AlReferralInfoRepository;
use App\Traits\CrudTrait;
use App\Traits\FileTrait;
use Exception;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;

use App\Repositories\AppServiceProductRepository;
use App\Repositories\AppServiceProductDetailsRepository;

class AppServiceDetailsService extends ApiBaseService
{
	use CrudTrait;

	/**
	 * @var $appServiceProductDetailsRepository
	 */
	protected $appServiceProductDetailsRepository;


	/**
	 * @var AppServiceProductDetailsRepository
	 */
	protected $appServiceProductRepository;
    /**
     * @var AlReferralInfoRepository
     */
    private $alReferralInfoRepository;
    private $imageFileViewerService;


    /**
     * AppServiceProductService constructor.
     * @param AppServiceProductDetailsRepository $appServiceProductDetailsRepository
     * @param AppServiceProductRepository $appServiceProductRepository
     * @param AlReferralInfoRepository $alReferralInfoRepository
     * @param ImageFileViewerService $imageFileViewerService
     */
	public function __construct(
	    AppServiceProductDetailsRepository $appServiceProductDetailsRepository,
        AppServiceProductRepository $appServiceProductRepository,
        AlReferralInfoRepository $alReferralInfoRepository,
        ImageFileViewerService  $imageFileViewerService
    ) {
		$this->appServiceProductDetailsRepository = $appServiceProductDetailsRepository;
		$this->appServiceProductRepository = $appServiceProductRepository;
		$this->alReferralInfoRepository = $alReferralInfoRepository;
		$this->imageFileViewerService = $imageFileViewerService;
		$this->setActionRepository($appServiceProductDetailsRepository);
	}


	/**
	 * [getProductInformationByID description]
	 * @param  [type] $product_id [description]
	 * @return [type]             [description]
	 */
	public function getProductInformationByID($product_id)
	{
		return $this->appServiceProductRepository->findOne($product_id, 'appServiceTab' );
	}

	/**
	 * [getProductDetailsBanner description]
	 * @param  [type] $product_id [description]
	 * @return [type]             [description]
	 */
	public function getProductDetailsOthersInfo($product_id)
	{
		$results = null;

		$get_product_details_banner = $this->appServiceProductDetailsRepository->appServiceDetailsOtherInfo($product_id);

		if( !empty($get_product_details_banner) ){
		    $detailKeyData = config('filesystems.moduleType.AppServiceProductDetail');
		    $imgData = $this->imageFileViewerService->prepareImageData($get_product_details_banner, $detailKeyData);
			$results['banner']['alt_text'] = $get_product_details_banner->alt_text;
			$results['banner']['alt_text_bn'] = $get_product_details_banner->alt_text_bn;
			$results['banner'] = array_merge($results['banner'], $imgData);

			$all_releated_products_ids = $get_product_details_banner->other_attributes;
			$all_releated_products_ids = isset($all_releated_products_ids['related_product_id']) ? $all_releated_products_ids['related_product_id'] : null;
			$results['releated_products']['title_en'] = $get_product_details_banner->title_en;
			$results['releated_products']['title_bn'] = $get_product_details_banner->title_bn;

			if( !empty($all_releated_products_ids) ){
			    $productList = [];
			    $get_all_releated_products = $this->appServiceProductRepository->findByIds($all_releated_products_ids);

                $productKeyData = config('filesystems.moduleType.AppServiceProduct');

                foreach ($get_all_releated_products as $product) {
                    $imgData = $this->imageFileViewerService->prepareImageData($product, $productKeyData);
                    $product = array_merge($product->toArray(), $imgData);

                    $productList[] = (object) $product;
                }

            }

			$results['releated_products']['products'] = !empty($productList) ? $productList : null;
		}

		return $results;

	}


	/**
	 * [getDetailsSectionComponents description]
	 * @param  [type] $product_id [description]
	 * @return [type]             [description]
	 */
	public function getDetailsSectionComponents($product_id, $component_type = [])
	{

		$data = null;

		$results = $this->appServiceProductDetailsRepository->getSectionsComponents($product_id);



		if( !empty($results) && count($results) > 0 ){

			foreach ($results as $value) {
				$sub_data = [];
				$parent_data['title_en'] = $value->title_en;
				$parent_data['title_bn'] = $value->title_bn;
				$parent_data['slug'] = $value->slug;
				$parent_data['section_type'] = $value->section_type;
				$sub_data['section_header'] = $parent_data;

				if( !empty($value->detailsComponent) && count($value->detailsComponent) > 0 ){


					foreach ($value->detailsComponent as $item) {

						$sub_item = [];

						$sub_item['title_en'] = $item->title_en;
						$sub_item['title_bn'] = $item->title_bn;
						$sub_item['slug'] = $item->slug;
						$sub_item['component_type'] = $item->component_type;
						$sub_item['description_en'] = $item->description_en;
						$sub_item['description_bn'] = $item->description_bn;
						$sub_item['editor_en'] = $item->editor_en;
						$sub_item['editor_bn'] = $item->editor_bn;
						$sub_item['image'] = !empty($item->image) ? config('filesystems.image_host_url') . $item->image : null;
						$sub_item['alt_text'] = $item->alt_text;

                                                $sub_item['video'] = NULL;

                                                $other_attr_array = json_decode($item->other_attributes, true);
                                                $sub_item['other_attributes'] = $item->other_attributes;

						if( !empty($item->other_attributes) ){

							foreach ($other_attr_array as $key => $value) {
								$sub_item[$key] = $value;
							}
						}

                                                if(!empty($item->video)){
                                                    if($other_attr_array['video_type'] == 'youtube_video'){
                                                        $sub_item['video'] = $item->video;
                                                    }else{
                                                        $sub_item['video'] = config('filesystems.image_host_url');
                                                    }
                                                }

						$sub_item['alt_links'] = $item->alt_links;

						// Multiple attributed formated
						if( $item->multiple_attributes != null && !empty($item->multiple_attributes) ){
							$res = json_decode($item->multiple_attributes, true);

							if( !empty($res) && count($res) > 0 ){

								$multi_res = array_map(function($value){

									if( isset($value['image_url']) ){
										$value['image_url'] = config('filesystems.image_host_url') . $value['image_url'];
									}


									if( $value['status'] == 0 ){
										return null;
									}

									return $value;

								}, $res);

								$final_multi_res = array_values(array_filter($multi_res));
								usort($final_multi_res, function($a, $b){return strcmp($a["display_order"], $b["display_order"]);});

								$sub_item['multiple_attributes'] = $final_multi_res;
							}
							else{
								$sub_item['multiple_attributes'] = null;
							}



						}
						else{
							$sub_item['multiple_attributes'] = null;
						}



						$sub_data['component'][] = $sub_item;

					}
				}
				else{
					$sub_data['component'] = null;
				}

				$data[] = $sub_data;

			}
		}



		return $data;
	}

	public function getDetails($slug)
    {
        $data = null;

        # get app and service product info
		$product_info = $this->appServiceProductRepository->getProductInformationBySlug($slug);

        # get dynamic image name
        $keyData = config('filesystems.moduleType.AppServiceProduct');
        $imgData = $this->imageFileViewerService->prepareImageData($product_info, $keyData);
        $product_info->image_url_en = isset($imgData['banner_image_web_en']) ? $imgData['banner_image_web_en'] : null;
        $product_info->image_url_bn = isset($imgData['banner_image_web_bn']) ? $imgData['banner_image_web_bn'] : null;

        $additional_details = $this->getProductDetailsOthersInfo($product_info->id);

		$data['tab_name'] = isset($product_info->appServiceTab->alias) ? $product_info->appServiceTab->alias : null;

		$data['page_header'] = $product_info->page_header;
		$data['page_header_bn'] = $product_info->page_header_bn;
		$data['schema_markup'] = $product_info->schema_markup;
		$data['url_slug'] = $product_info->url_slug;
		$data['url_slug_bn'] = $product_info->url_slug_bn;

        $data['section_banner']['section_banner_info'] = isset($additional_details['banner']) ? $additional_details['banner'] : null;

        $data['section_banner']['app_info'] = !empty($product_info) ? $product_info : null;

        # Get App tab details component
        if( $product_info->appServiceTab->alias == 'app' ){
            # Get component "text with image right", "text with image bottom"
            $data['section_component'] = $this->getDetailsSectionComponents($product_info->id);

            // $data['section_component']['app_view'] = $this->appServiceDetailsService->getDetailsSectionComponents($product_id, ['text_with_image_right', 'text_with_image_bottom']);

            // $data['section_component']['slider_view'] = $this->appServiceDetailsService->getDetailsSectionComponents($product_id, ['slider_text_with_image_right']);

            // $data['section_component']['others_view'] = $this->appServiceDetailsService->getDetailsSectionComponents($product_id, ['title_text_editor', 'video_with_text_right', 'multiple_image_banner']);
        }
        elseif( $product_info->appServiceTab->alias == 'vas' ){

            $data['section_component'] = $this->getDetailsSectionComponents($product_info->id);
        }
        elseif( $product_info->appServiceTab->alias == 'financial' ){

            $data['section_component'] = $this->getDetailsSectionComponents($product_info->id);
        }
        elseif( $product_info->appServiceTab->alias == 'others' ){

            $data['section_component'] = $this->getDetailsSectionComponents($product_info->id);
        }
        else{
            $data['section_component'] = null;
        }

        $data['related_products'] = isset($additional_details['releated_products']) ? $additional_details['releated_products'] : null;

        $referralInfo = $this->alReferralInfoRepository->findOneByProperties(['app_id' => $product_info->id, 'status' => 1]);

        $data['referral_info'] = isset($referralInfo) ? $referralInfo : null;

        return $this->sendSuccessResponse($data, 'App and Service Details Info');
    }
}
