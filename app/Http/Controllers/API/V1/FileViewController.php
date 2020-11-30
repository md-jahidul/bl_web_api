<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;

//use App\Models\OfferCategory;
use App\Models\OfferCategory;
use App\Traits\FileTrait;
use Illuminate\Http\JsonResponse;

use App\Models;


class FileViewController extends Controller
{
    use FileTrait;

    public function showFile($modelName, $fileName)
    {
        $fileName = explode('.', $fileName)[0];
        $model = str_replace('.', '', "App\Models\.$modelName");

        $data = config('filesystems.moduleType.'.$modelName);

        $offers = $model::where($data['image_name_en'], $fileName)->orWhere($data['image_name_bn'], $fileName)->first();
        return $this->view($offers->{ $data['exact_path'] });
    }

    /**
     * @return JsonResponse
     */
    public function offerList()
    {
//        $singleData = OfferCategory::where("banner_alt_text', $fileName)->first();

        $offers = OfferCategory::where('parent_id', 0)->get();

        foreach ($offers as $cat) {
//            $encrypted = base64_encode($cat->banner_image_url);
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
