<?php

namespace App\Services;

use App\Repositories\LoginLandingPageRepository;

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
        try {
            $data = $this->loginLandingPageRepository->findLoginPageBanner();
            return $this->sendSuccessResponse($data, 'Login Page Banner');
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage(), []);
        }
    }

}
