<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;

//use App\Models\OfferCategory;
use App\Models\OfferCategory;
use App\Services\ImageFileViewerService;
use App\Traits\FileTrait;
use Illuminate\Http\JsonResponse;

use App\Models;


class ImageFileViewerController extends Controller
{

    /**
     * @var ImageFileViewerService
     */
    private $fileViewerService;

    /**
     * ImageFileViewerService constructor.
     * @param ImageFileViewerService $fileViewerService
     */
    public function __construct(ImageFileViewerService $fileViewerService)
    {
        $this->fileViewerService = $fileViewerService;
    }

    public function bannerImageWeb($modelName, $fileName)
    {
        return $this->fileViewerService->bannerImageWeb($modelName, $fileName);

    }

    public function bannerImageMobile($modelName, $fileName)
    {
        //
    }

    /**
     * @return JsonResponse
     */
    public function offerList()
    {
//        $singleData = OfferCategory::where("banner_alt_text', $fileName)->first();

        $offers = OfferCategory::where('parent_id', 0)->get();

        foreach ($offers as $cat) {
            $extension = explode('.', $cat->banner_image_url);
            $extension = isset($extension[1]) ? ".".$extension[1] : null;
            $fileNameEn = $cat->banner_alt_text . $extension;
            $fileNameBn = $cat->banner_alt_text_bn . $extension;

            $model = "OfferCategory";

            $data [] = [
                'banner_image_url' =>  request()->root() . "/api/v1/show-file/$model/".$fileNameEn,
                'banner_image_url_bn' => request()->root() . "/api/v1/show-file/$model/".$fileNameBn,
                'banner_image_mobile_en' => request()->root() . "/api/v1/show-file/$model/".$fileNameBn,
            ];
        }

        return $data;
    }

}
