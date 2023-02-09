<?php

/**
 * Created by PhpStorm.
 * User: bs-205
 * Date: 18/08/19
 * Time: 17:07
 */

namespace App\Repositories;

use App\Models\AlSlider;
use App\Models\AppServiceCategory;
use App\Models\AppServiceProduct;
use App\Models\AppServiceProductDetail;

class AppServiceProductDetailsRepository extends BaseRepository
{
    public $modelName = AppServiceProductDetail::class;

    public function findSection($product_id)
    {
        return $this->model->where('product_id', $product_id)
            ->whereNotNull('section_name')
            ->get();
    }

    public function fixedSection($product_id)
    {
        return $this->model->where('product_id', $product_id)
            ->whereNull('section_name')
            ->first();
    }

    public function checkFixedSection($product_id)
    {
        return $this->model
            ->where('product_id', $product_id)
            ->whereNotNull('category')
            ->first();
    }


    public function appServiceDetailsOtherInfo($product_id)
    {
        return $this->model->where('product_id', $product_id)
            ->where('category', 'app_banner_fixed_section')
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->first([
                'title_en',
                'title_bn',
                'image',
                'banner_image_mobile',
                'alt_text',
                'other_attributes',
                'multiple_component',
                'banner_title_en',
                'banner_title_bn',
                'banner_desc_en',
                'banner_desc_bn',
            ]);
    }


    public function getSectionsComponents($product_id, $component_type = [])
    {
        if( empty($component_type) ){
            return $this->model->with('detailsComponent')->where('product_id', $product_id)
                ->where('category', 'component_sections')
                ->where('status', 1)
                ->whereNull('deleted_at')
                ->orderBy('section_order', 'asc')
                ->get();
        }
        else{

            return $this->model->whereHas('detailsComponent', function($query) use ($component_type){

                foreach ($component_type as $key => $value) {

                    if( $key == 0 ){
                        $query->where('component_type', $value);
                    }
                    else{
                        $query->orWhere('component_type', $value);
                    }
                }
            })->where('product_id', $product_id)
                ->whereNull('category')
                ->where('status', 1)
                ->whereNull('deleted_at')
                ->orderBy('section_order', 'asc')
                ->get();

        }

    }
}
