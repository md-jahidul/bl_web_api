<?php

namespace App\Http\Controllers\API\V1;

use App\Exceptions\IdpAuthException;
use App\Http\Controllers\Controller;
use App\Models\OfferCategory;
use App\Services\AboutUsService;
use App\Traits\FileTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class FileViewController extends Controller
{
    use FileTrait;

    public function showFile($fileName)
    {
        $offers = OfferCategory::where('banner_alt_text', $fileName)->first();
        return $this->view($offers->banner_image_url);
    }

    /**
     * @return JsonResponse
     */
    public function viewFile(Request $request)
    {
//        $fileName = $request['file_name'];

//        $singleData = OfferCategory::where('banner_alt_text', $fileName)->first();
        $offers = OfferCategory::where('parent_id', 0)->get();

        foreach ($offers as $cat) {
//            dd($cat->banner_alt_text);
            $data [] = [
                'banner_image_url_en' =>  $request->root() . "/api/v1/show-file/" . $cat->banner_alt_text
//                'banner_image_url_en' => $cat->banner_image_url
            ];
        }

        return $data;
//        dd($offers->banner_image_url);
    }

}
