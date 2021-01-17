<?php

namespace App\Services;

use App\Http\Resources\AboutPriyojonResource;
use App\Repositories\AboutPageRepository;
use App\Repositories\AboutPriyojonRepository;
use App\Traits\FileTrait;
use Illuminate\Support\Facades\DB;


class ImageFileViewerService extends ApiBaseService
{
    use FileTrait;

    public function imageViewer($bannerType, $modelKey, $fileName)
    {
        $modelKey = config('filesystems.modelKeyList.' . $modelKey);
        $fileName = explode('.', $fileName)[0];
        $data = config('filesystems.moduleType.' . $modelKey);
        $modelName = $data['model'];
        $model = str_replace('.', '', "App\Models\.$modelName");


        if (isset($data['component_page_type'])){
            $dd = DB::table('components')
//            $dd = $model::
            ->where('page_type', $data['component_page_type'])
//                ->where('section_details_id', 13)
                ->whereJsonContains('multiple_attributes', ['img_name_bn' => 'image-name-bangla-2'])
                ->first();
            dd($dd);

            dd($fileName);
            $offers = $model::where($data['image_name_en'], $fileName)->orWhere($data['image_name_bn'], $fileName)->first();

        }

        $offers = $model::where($data['image_name_en'], $fileName)->orWhere($data['image_name_bn'], $fileName)->first();
        return ($bannerType == "banner-web") ? $this->view($offers->{$data['exact_path_web']}) : $this->view($offers->{$data['exact_path_mobile']});
    }

    public function getBannerImage($bannerType, $modelName, $fileName)
    {
        return $this->imageViewer($bannerType, $modelName, $fileName);
        try {
        } catch (\Exception $exception){
            return abort(404);
        }
    }

    public function prepareImageData($value, $keyData)
    {
        $extension = explode('.', $value[$keyData['exact_path_web']]);
        $extension = isset($extension[1]) ? ".".$extension[1] : null;

        $fileNameEn = $value[$keyData['image_name_en']] . $extension;
        $fileNameBn = $value[$keyData['image_name_bn']] . $extension;

        $model = $keyData['model-key'];
        $imgData = [];

        if (isset($keyData['image_type']) && $keyData['image_type'] == "body-image") {
            if (!empty($value[$keyData['exact_path_web']])) {
                $bannerType = "images";
                $imgData['image_url_en'] = "$bannerType/$model/$fileNameEn";
                $imgData['image_url_bn'] = "$bannerType/$model/$fileNameBn";
            }
        } else {
            if (!empty($value[$keyData['exact_path_web']])) {
                $imageType = "banner-web";
                $imgData['banner_image_web_en'] = "$imageType/$model/$fileNameEn";
                $imgData['banner_image_web_bn'] = "$imageType/$model/$fileNameBn";
            }
            if (!empty($value[$keyData['exact_path_mobile']])) {
                $bannerType = "banner-mobile";
                $imgData['banner_image_mobile_en'] = "$bannerType/$model/$fileNameEn";
                $imgData['banner_image_mobile_bn'] = "$bannerType/$model/$fileNameBn";
            }
        }

        return $imgData;
    }
}
