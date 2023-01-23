<?php

namespace App\Services;

use App\Repositories\AlBannerRepository;

/**
 * Class AlBannerService
 * @package App\Services
 */
class AlBannerService extends ApiBaseService
{

    /**
     * @var AlBannerRepository
     */
    protected $alBannerRepository;


    /**
     * AlBannerService constructor.
     * @param AlBannerRepository $alBannerRepository
     */
    public function __construct(AlBannerRepository $alBannerRepository)
    {
        $this->alBannerRepository = $alBannerRepository;
    }

}
