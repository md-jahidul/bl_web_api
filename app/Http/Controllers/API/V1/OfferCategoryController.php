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

    public function offerCategories() {

        return $this->offerCategoryService->offerCatList();

        $tags = TagCategory::all();
        $sim = SimCategory::all();
        $offer = OfferCategory::where('parent_id', 0)->with('children')->get();

        if (!empty($offer)) {
            $offer_final = array_map(function($value) {
                if (!empty($value['banner_image_url'])) {

                    $encrypted = base64_encode($value['banner_image_url']);

                    $extension = explode('.', $value['banner_image_url']);
                    $extension = isset($extension[1]) ? ".".$extension[1] : null;
                    $fileName = $value['banner_alt_text'] . $extension;


                    $value['banner_image_url'] = request()->root() . "/api/v1/show-file/$encrypted/" . $fileName;
//                    $value['banner_image_url'] = config('filesystems.image_host_url') . $value['banner_image_url'];
                }
                if (!empty($value['banner_image_mobile'])) {
                    $value['banner_image_mobile'] = config('filesystems.image_host_url') . $value['banner_image_mobile'];
                }
                return $value;
            }, $offer->toArray());
        } else {
            $offer_final = [];
        }

        $duration = DurationCategory::all();

        return response()->json(
            [
                'status' => 200,
                'success' => true,
                'message' => 'Data Found!',
                'data' => [
                    'tag' => $tags,
                    'sim' => $sim,
                    'offer' => $offer_final,
                    'duration' => $duration
                ]
            ]
        );
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
