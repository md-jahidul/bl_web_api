<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\OfferCategory;
use App\Traits\FileTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Crypt;


class FileViewController extends Controller
{
    use FileTrait;

    public function showFile($dirLocation, $fileName)
    {
        $fileName = explode('.', $fileName)[0];

        $decode = base64_decode($dirLocation);

//        $offers = OfferCategory::where('banner_alt_text', $fileName)->first();
//        return $this->view($offers->banner_image_url);

        return $this->view($decode);
    }

    /**
     * @return JsonResponse
     */
    public function viewFile()
    {
//        $singleData = OfferCategory::where('banner_alt_text', $fileName)->first();

        $offers = OfferCategory::where('parent_id', 0)->get();

        foreach ($offers as $cat) {
            $encrypted = base64_encode($cat->banner_image_url);

            $extension = explode('.', $cat->banner_image_url);
            $extension = isset($extension[1]) ? ".".$extension[1] : null;
            $fileName = $cat->banner_alt_text . $extension;

            $data [] = [
                'banner_image_url_en' =>  request()->root() . "/api/v1/show-file/$encrypted/" . $fileName
//                'banner_image_url_en' => $cat->banner_image_url
            ];
        }

        return $data;
//        dd($offers->banner_image_url);
    }

}
