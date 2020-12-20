<?php

namespace App\Services;

use App\Http\Resources\AboutPriyojonResource;
use App\Repositories\AboutPageRepository;
use App\Repositories\AboutPriyojonRepository;
use App\Repositories\LmsAboutBannerRepository;
use App\Repositories\LmsBenefitRepository;
use App\Traits\CrudTrait;
use App\Traits\FileTrait;
use Illuminate\Database\QueryException;
use mysql_xdevapi\Exception;


class ImageFileViewerService extends ApiBaseService
{
    use FileTrait;

    public function imageViewer($bannerType, $modelName, $fileName)
    {
        $modelKey = config('filesystems.modelList.' . $modelName);
        $fileName = explode('.', $fileName)[0];
        $model = str_replace('.', '', "App\Models\.$modelKey");
        $data = config('filesystems.moduleType.' . $modelKey);

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

        $model = $keyData['model'];
        $imgData = [];

        if (!empty($value[$keyData['exact_path_web']])) {
            $bannerType = "banner-web";
            $imgData['banner_image_web_en'] = "/$bannerType/$model/$fileNameEn";
            $imgData['banner_image_web_bn'] = "/$bannerType/$model/$fileNameBn";
        }

        if (!empty($value[$keyData['exact_path_mobile']])) {
            $bannerType = "banner-mobile";
            $imgData['banner_image_mobile_en'] = "/$bannerType/$model/$fileNameEn";
            $imgData['banner_image_mobile_bn'] = "/$bannerType/$model/$fileNameBn";
        }

        return $imgData;
    }
}
