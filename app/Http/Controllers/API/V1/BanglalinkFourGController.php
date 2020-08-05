<?php

namespace App\Http\Controllers\API\V1;

use App\Exceptions\IdpAuthException;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ProductService;
use DB;
use App\Services\AboutUsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class BanglalinkFourGController extends Controller
{
    /**
     * @var ProductService
     */
    private $productService;

    /**
     * AboutUsController constructor.
     * @param ProductService $productService
     */
    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * @return JsonResponse
     */
    public function getFourGInternet($type, $last_id = null)
    {
       return $this->productService->fourGInternet($type, $last_id);

        if ($last_id){
            $products = Product::where('offer_category_id', 1)
                ->where('is_four_g_offer', 1)
                ->where('status', 1)
                ->where('id', '>', $last_id)
                ->where('special_product', 0)
                ->startEndDate()
                ->productCore()
                ->category($type)
                ->limit(2)
                ->get();
        } else {
            $products = Product::where('offer_category_id', 1)
                ->where('is_four_g_offer', 1)
                ->where('status', 1)
                ->where('special_product', 0)
                ->startEndDate()
                ->productCore()
                ->category($type)
//                ->limit(2)
                ->get();
        }


        return $products;

//        return $this->aboutUsService->getAboutBanglalink();
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getAboutManagement()
    {
//        return $this->aboutUsService->getAboutManagement();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws IdpAuthException
     */
    public function getEcareersInfo()
    {
        return $this->aboutUsService->getEcareersInfo();
    }


}
