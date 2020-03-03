<?php

namespace App\Services;

//use App\Repositories\AppServiceProductegoryRepository;

use App\Traits\CrudTrait;
use App\Traits\FileTrait;
use Exception;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;

use App\Repositories\AppServiceProductRepository;
use App\Repositories\AppServiceProductDetailsRepository;

class AppServiceDetailsService
{
    use CrudTrait;
    use FileTrait;


    /**
     * @var $appServiceProductDetailsRepository
     */
    protected $appServiceProductDetailsRepository;
    

    /**
     * @var AppServiceProductDetailsRepository
     */
    protected $appServiceProductRepository;


    /**
     * AppServiceProductService constructor.
     * @param AppServiceProductDetailsRepository $appServiceProductDetailsRepository
     */
    public function __construct(AppServiceProductDetailsRepository $appServiceProductDetailsRepository, AppServiceProductRepository $appServiceProductRepository)
    {
        $this->appServiceProductDetailsRepository = $appServiceProductDetailsRepository;
        $this->appServiceProductRepository = $appServiceProductRepository;
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
            $results['banner']['image'] = !empty($get_product_details_banner->image) ? config('filesystems.image_host_url') . $get_product_details_banner->image : null;
            $results['banner']['alt_text'] = $get_product_details_banner->alt_text;
            
            $all_releated_products_ids = $get_product_details_banner->other_attributes;
            $all_releated_products_ids = isset($all_releated_products_ids['related_product_id']) ? $all_releated_products_ids['related_product_id'] : null;
            $get_all_releated_products = $this->appServiceProductRepository->findByIds($all_releated_products_ids);
            $results['releated_products']['title_en'] = $get_product_details_banner->title_en;
            $results['releated_products']['title_bn'] = $get_product_details_banner->title_bn;
            $results['releated_products']['products'] = !empty($get_all_releated_products) ? $get_all_releated_products : null;
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

        $results = $this->appServiceProductDetailsRepository->getSectionsComponents($product_id, $component_type);

        

        if( !empty($results) && count($results) > 0 ){

            foreach ($results as $value) {
                
                $sub_data = [];

                $parent_data['title_en'] = $value->title_en;
                $parent_data['title_bn'] = $value->title_bn;
                $parent_data['slug'] = $value->slug;

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
                        $sub_item['video'] = !empty($item->video) ? config('filesystems.image_host_url') . $item->video : null;
                        $sub_item['alt_links'] = $item->alt_links;

                        
                        if( $item->multiple_attributes != null && !empty($item->multiple_attributes) ){
                            $res = json_decode($item->multiple_attributes, true);
                            
                            if( !empty($res) && count($res) > 0 ){

                                $multi_res = array_map(function($value){

                                    if( isset($value['image_url']) ){
                                        $value['image_url'] = config('filesystems.image_host_url') . $value['image_url'];
                                    }

                                    // if( $value['status'] == 0 ){
                                    // }
                                    
                                    return $value;

                                }, $res);

                                $sub_item['multiple_attributes'] = array_values($multi_res);
                            }
                            else{
                                $sub_item['multiple_attributes'] = null;
                            }


                        }
                        else{
                            $sub_item['multiple_attributes'] = null;
                        }
                        
                        $sub_item['other_attributes'] = $item->other_attributes;

                        if( !empty($item->other_attributes) && count($item->other_attributes) >0 ){
                            foreach ($item->other_attributes as $key => $value) {
                                $sub_item[$key] = $value;
                            }
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

    
}
