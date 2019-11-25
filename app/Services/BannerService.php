<?php

namespace App\Services;

use App\Http\Resources\BannerResource;
use App\Repositories\BannerRepository;
use Exception;

/**
 * Class BannerService
 * @package App\Services
 */
class BannerService extends ApiBaseService
{

    /**
     * @var BannerRepository
     */
    protected $bannerRepository;


    /**
     * BannerService constructor.
     * @param BannerRepository $bannerRepository
     */
    public function __construct(BannerRepository $bannerRepository)
    {
        $this->bannerRepository = $bannerRepository;
    }

    /**
     * Request for Banner info
     *
     * @return mixed|string
     */
    public function getBannerInfo()
    {
        try {
            $data = $this->bannerRepository->getBannerInfo();
            $formatted_data = BannerResource::collection($data);
            return $this->sendSuccessResponse($formatted_data, 'Banner Info');
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }
}
