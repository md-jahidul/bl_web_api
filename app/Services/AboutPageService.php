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
     */
    public function __construct(
        AboutPageRepository $aboutPageRepository,
        LmsBenefitRepository $benefitRepository,
        LmsAboutBannerRepository $lmsAboutBannerRepository,
        PriyojonRepository $priyojonRepository
    ) {
        $this->aboutPageRepository = $aboutPageRepository;
        $this->benefitRepository = $benefitRepository;
        $this->lmsAboutBannerRepository = $lmsAboutBannerRepository;
        $this->priyojonRepository = $priyojonRepository;
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
                    $benefits = $this->benefitRepository->findByProperties(
                        ['page_type' => $slug, 'status' => 1],
                        ['page_type', 'title_en', 'title_bn', 'image_url', 'alt_text_en']
                    );

                    $priyojonAlias = $slug == self::PRIYOJON ? 'about-priyojon' : 'about';

                    $priyojonMenu = $this->priyojonRepository->findMenuForSlug($priyojonAlias);

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

    public function lmsAboutBanner($slug)
    {
        $data = $this->lmsAboutBannerRepository->findOneByProperties(['page_type' => $slug], ['page_type', 'banner_image_url', 'banner_mobile_view', 'alt_text_en']);
        return $this->sendSuccessResponse($data, 'Loyalty About Us info');
    }
}
