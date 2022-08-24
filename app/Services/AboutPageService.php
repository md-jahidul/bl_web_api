<?php

namespace App\Services;

use App\Http\Resources\AboutPriyojonResource;
use App\Repositories\AboutPageRepository;
use App\Repositories\AboutPriyojonRepository;
use App\Repositories\LmsAboutBannerRepository;
use App\Repositories\LmsBenefitRepository;
use App\Repositories\PriyojonRepository;
use App\Traits\CrudTrait;
use Illuminate\Database\QueryException;


class AboutPageService extends ApiBaseService
{
    use CrudTrait;

    /**
     * @var $aboutPageRepository
     */
    protected $aboutPageRepository;

    /**
     * @var $priyojonRepository
     */
    protected $priyojonRepository;
    /**
     * @var $imageFileViewerService
     */
    protected $imageFileViewerService;

    protected const PRIYOJON = "priyojon";
    protected const REWARD_POINTS = "reward_points";
    /**
     * @var LmsBenefitRepository
     */
    private $benefitRepository;
    /**
     * @var LmsAboutBannerRepository
     */
    private $lmsAboutBannerRepository;

    /**
     * AboutPageService constructor.
     * @param AboutPageRepository $aboutPageRepository
     * @param LmsBenefitRepository $benefitRepository
     * @param LmsAboutBannerRepository $lmsAboutBannerRepository
     * @param PriyojonRepository $priyojonRepository
     * @param ImageFileViewerService $imageFileViewerService
     */
    public function __construct(
        AboutPageRepository $aboutPageRepository,
        LmsBenefitRepository $benefitRepository,
        LmsAboutBannerRepository $lmsAboutBannerRepository,
        PriyojonRepository $priyojonRepository,
        ImageFileViewerService $imageFileViewerService
    ) {
        $this->aboutPageRepository = $aboutPageRepository;
        $this->benefitRepository = $benefitRepository;
        $this->lmsAboutBannerRepository = $lmsAboutBannerRepository;
        $this->priyojonRepository = $priyojonRepository;
        $this->imageFileViewerService = $imageFileViewerService;
        $this->setActionRepository($aboutPageRepository);
    }

    /**
     * @param $slug
     * @return mixed
     */
    public function aboutDetails($slug)
    {
        try {
            if ($slug == self::PRIYOJON || $slug == self::REWARD_POINTS){
                $aboutDetails = $this->aboutPageRepository->findDetail($slug);
                if (!empty($aboutDetails)) {
                   $aboutDetails = $this->getAboutPageImgData($aboutDetails);

                    $benefits = $this->benefitRepository->findByProperties(
                        ['page_type' => $slug, 'status' => 1],
                        ['page_type', 'title_en', 'title_bn', 'image_url', 'alt_text_en']
                    );

                    $priyojonAlias = $slug == self::PRIYOJON ? 'about-priyojon' : 'about';

                    $priyojonMenu = $this->priyojonRepository->getMenuForSlug($priyojonAlias);

                    $data = [
                        "details" => $aboutDetails,
                        "benefits" => $benefits,
                        'alias' => $priyojonMenu->alias,
                        'url_slug_en' => $priyojonMenu->url_slug_en,
                        'url_slug_bn' => $priyojonMenu->url_slug_bn,
                    ];

                    return $this->sendSuccessResponse($data, 'Loyalty About Us info');
                }
            }
            return response()->error("Invalid Parameter");
        } catch (QueryException $exception) {
            return response()->error("Something Wrong", $exception);
        }
    }

    public function  getAboutPageImgData($aboutDetails)
    {
        $data = $aboutDetails;
         foreach ($aboutDetails as $key => $detail) {
                    $leftImgData = [];
                    $rightImgData = [];

                    $leftKeyData = config('filesystems.moduleType.AboutPageLeftImg');
                    $lrightKeyData = config('filesystems.moduleType.AboutPageRightImg');

                    $leftImgData = array_merge($leftImgData, $this->imageFileViewerService->prepareImageData($detail, $leftKeyData));
                    $rightImgData = array_merge($rightImgData, $this->imageFileViewerService->prepareImageData($detail, $lrightKeyData));

                    $detail->left_img= $leftImgData;
                    $detail->right_img = $rightImgData;
                    unset($detail->left_img_name_en, $detail->left_img_name_bn, $detail->right_img_name_en,
                          $detail->right_img_name_bn, $detail->left_side_img, $detail->right_side_ing);

                    $data[$key] = $detail;
                 }

         return $data;
    }

    public function lmsAboutBanner($slug)
    {
        $data = $this->lmsAboutBannerRepository->findOneByProperties(['page_type' => $slug], ['page_type', 'banner_image_url',
            'banner_mobile_view', 'alt_text_en', 'alt_text_bn', 'banner_name', 'banner_name_bn']);

        $keyData = config('filesystems.moduleType.LmsAboutBannerImage');

        $data = array_merge($data->toArray(), $this->imageFileViewerService->prepareImageData($data, $keyData));
        unset($data['banner_image_url'], $data['banner_mobile_view']);

        return $this->sendSuccessResponse($data, 'Loyalty About Us info');
    }
}
