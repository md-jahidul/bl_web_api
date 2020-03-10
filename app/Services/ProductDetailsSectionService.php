<?php

namespace App\Services\Assetlite;

//use App\Repositories\AppServiceProductegoryRepository;

use App\Enums\HttpStatusCode;
use App\Repositories\AppServiceProductDetailsRepository;
use App\Repositories\ComponentRepository;
use App\Repositories\ProductDetailsSectionRepository;
use App\Services\ApiBaseService;
use App\Traits\CrudTrait;
use App\Traits\FileTrait;
use Exception;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;

class ProductDetailsSectionService extends ApiBaseService
{
    use CrudTrait;
    /**
     * @var $productDetailsSectionRepository
     */
    protected $productDetailsSectionRepository;

    /**
     * @var $componentRepository
     */
    protected $componentRepository;


    /**
     * ProductDetailsSectionService constructor.
     * @param ProductDetailsSectionRepository $productDetailsSectionRepository
     * @param ComponentRepository $componentRepository
     */
    public function __construct(
        ProductDetailsSectionRepository $productDetailsSectionRepository,
        ComponentRepository $componentRepository
    ) {
        $this->productDetailsSectionRepository = $productDetailsSectionRepository;
        $this->componentRepository = $componentRepository;
        $this->setActionRepository($productDetailsSectionRepository);
    }

    public function bindDynamicValues($obj = null, $json_data = 'other_attributes', $data = null)
    {
       return $obj['test_column'];

//        if (!empty($obj->{$json_data})) {
//            foreach ($obj->{$json_data} as $key => $value) {
//                $obj->{$key} = $value;
//            }
//            unset($obj->{$json_data});
//        }
        // Product Core Data BindDynamicValues
//        $data = json_decode($data);
//
//        if (!empty($data)) {
//            foreach ($data as $key => $value) {
//                $obj->{$key} = $value;
//            }
//            return $obj;
//        }
    }


    public function productDetails($productId)
    {
        $sections = $this->productDetailsSectionRepository->section($productId);

        foreach ($sections as $section){
            ($section->section_type == "tab_section") ? $isTab = true : $isTab = false;
        }

        $data['header'] = [
            "banner_image" => null,
            "isTab" => isset($isTab) ? $isTab : null
        ];

//        $data['section'] = $sections;

        foreach ($sections as $category => $section) {
            $data['section'] = $sections;

            return $data;

//            foreach ($section['components'] as $component)
//            {
//                $this->bindDynamicValues();
//            }


//            if ($section->section_type == "tab_section") {
//                $data['tabs'] = $sections;
//            } else {
//            }

//            $data['component'] = null;

//            foreach ($section->components as $item){
//
//                foreach ($item->multiple_attributes['image'] as $key => $img){
//
//                    return $key;
//                }
//            }
        }

//        dd($data);

        return $this->sendSuccessResponse($data, 'Product details page', [], HttpStatusCode::SUCCESS);

    }

}
