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


class ImageFileViewerService extends ApiBaseService
{
    use FileTrait;

//    public function


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
        return $this->imageViewer($bannerType, $modelName, $fileName);
    }

}
