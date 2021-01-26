<?php

namespace App\Services;

use App\Http\Resources\AboutPriyojonResource;
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

        // Body Section Image
        if (isset($data['image_type']) && $data['image_type'] == 'body-image'){
            $offers = $model::where($data['image_name_en'], $fileName)
                ->orWhere($data['image_name_bn'], $fileName);
            if (isset($data['component_page_type']))
                $offers = $offers->where('page_type', $data['component_page_type']);
            $imgBasePath = $offers->first();
            return $this->view($imgBasePath->{$data['exact_path_web']});
        }

        // Banner Section Image
        $offers = $model::where($data['image_name_en'], $fileName)->orWhere($data['image_name_bn'], $fileName)->first();
        return ($bannerType == "banner-web") ? $this->view($offers->{$data['exact_path_web']}) : $this->view($offers->{$data['exact_path_mobile']});
    }

    public function getBannerImage($bannerType, $modelName, $fileName)
    {
        try {
            return $this->imageViewer($bannerType, $modelName, $fileName);
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
                $imgData['image_url_en'] = ($value[$keyData['image_name_en']]) ? "$bannerType/$model/$fileNameEn" : '/uploads/' . $value[$keyData['exact_path_web']];
                $imgData['image_url_bn'] = ($value[$keyData['image_name_bn']]) ? "$bannerType/$model/$fileNameBn" : '/uploads/' . $value[$keyData['exact_path_web']];
            }
        } else {
            if (!empty($value[$keyData['exact_path_web']])) {
                $imageType = "banner-web";
                $imgData['banner_image_web_en'] = ($value[$keyData['image_name_en']]) ? "$imageType/$model/$fileNameEn" : '/uploads/' . $value[$keyData['exact_path_web']];
                $imgData['banner_image_web_bn'] = ($value[$keyData['image_name_bn']]) ? "$imageType/$model/$fileNameBn" : '/uploads/' . $value[$keyData['exact_path_web']];
            }
            if (!empty($value[$keyData['exact_path_mobile']])) {
                $bannerType = "banner-mobile";
                $imgData['banner_image_mobile_en'] = ($value[$keyData['image_name_en']]) ? "$bannerType/$model/$fileNameEn" : '/uploads/' . $value[$keyData['exact_path_mobile']];
                $imgData['banner_image_mobile_bn'] = ($value[$keyData['image_name_bn']]) ? "$bannerType/$model/$fileNameBn" : '/uploads/' . $value[$keyData['exact_path_mobile']];
            }
        }

        return $imgData;
    }
}
