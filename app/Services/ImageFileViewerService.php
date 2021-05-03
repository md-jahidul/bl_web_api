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

//
//    /**
//     * @var $aboutPageRepository
//     */
//    protected $aboutPageRepository;
//
//    protected const PRIYOJON = "priyojon";
//    protected const REWARD_POINTS = "reward_points";
//    /**
//     * @var LmsBenefitRepository
//     */
//    private $benefitRepository;
//    /**
//     * @var LmsAboutBannerRepository
//     */
//    private $lmsAboutBannerRepository;
//
//    /**
//     * AboutPageService constructor.
//     * @param AboutPageRepository $aboutPageRepository
//     * @param LmsBenefitRepository $benefitRepository
//     * @param LmsAboutBannerRepository $lmsAboutBannerRepository
//     */
//    public function __construct(
//        AboutPageRepository $aboutPageRepository,
//        LmsBenefitRepository $benefitRepository,
//        LmsAboutBannerRepository $lmsAboutBannerRepository
//    ) {
//        $this->aboutPageRepository = $aboutPageRepository;
//        $this->benefitRepository = $benefitRepository;
//        $this->lmsAboutBannerRepository = $lmsAboutBannerRepository;
//        $this->setActionRepository($aboutPageRepository);
//    }

    public function imageViewer($modelName, $fileName)
    {
        $fileName = explode('.', $fileName)[0];
        $model = str_replace('.', '', "App\Models\.$modelName");

        $data = config('filesystems.moduleType.'.$modelName);

        $offers = $model::where($data['image_name_en'], $fileName)->orWhere($data['image_name_bn'], $fileName)->first();
        return $this->view($offers->{ $data['exact_path'] });
    }

    public function bannerImageWeb($modelName, $fileName)
    {
        return $this->imageViewer($modelName, $fileName);
    }

}
