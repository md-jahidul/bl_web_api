<?php

namespace App\Services;

use App\Repositories\LoginLandingPageRepository;
use Exception;

class LoginLandingPageService extends ApiBaseService
{

    /**
     * @var LoginLandingPageRepository
     */
    protected $loginLandingPageRepository;


    /**
     * Banglalink loginLandingPageRepository constructor.
     * @param loginLandingPageRepository $loginLandingPageRepository
     */
    public function __construct(LoginLandingPageRepository $loginLandingPageRepository)
    {
        $this->loginLandingPageRepository = $loginLandingPageRepository;
    }


    /**
     * Version Info
     * @return mixed
     */
    public function getLoginPageBanner()
    {
        $data = $this->loginLandingPageRepository->findLoginPageBanner();
        return $this->sendSuccessResponse($data, 'Login Page Banner');
    }

}
