<?php

namespace App\Http\Controllers\API\V1;

use App\Exceptions\IdpAuthException;
use App\Http\Controllers\Controller;
use App\Models\DurationCategory;
use App\Models\OfferCategory;
use App\Models\SimCategory;
use App\Models\TagCategory;
use App\Services\AboutUsService;
use App\Services\OfferCategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class OfferCategoryController extends Controller
{
    /**
     * @var OfferCategoryService
     */
    protected $offerCategoryService;

    /**
     * OfferCategoryController constructor.
     * @param OfferCategoryService $offerCategoryService
     */
    public function __construct(OfferCategoryService $offerCategoryService)
    {
        $this->offerCategoryService = $offerCategoryService;
    }

    public function offerCategories()
    {
        return $this->offerCategoryService->offerCatList();
    }

    /**
     * @param $type
     * @return JsonResponse
     */
    public function getPackageRelatedProduct($type)
    {
        return $this->offerCategoryService->relatedProducts($type);
    }
}
