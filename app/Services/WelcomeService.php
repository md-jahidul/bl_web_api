<?php

namespace App\Services;

use App\Repositories\WelcomeInfoRepository;
use Exception;

class WelcomeService extends ApiBaseService
{

    /**
     * @var welcomeInfoRepository
     */
    protected $welcomeInfoRepository;


    /**
     * WelcomeService constructor.
     * @param WelcomeInfoRepository $welcomeInfoRepository
     */
    public function __construct(WelcomeInfoRepository $welcomeInfoRepository)
    {
        $this->welcomeInfoRepository = $welcomeInfoRepository;
    }


    /**
     * Retrieve guest welcome info
     *
     * @return mixed|string
     */
    public function getGuestWelcomeInfo()
    {
        try {
            $data = $this->welcomeInfoRepository->getGuestWelcomeInfo();
            return $this->sendSuccessResponse($data, 'Guest Welcome Info');
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }


    /**
     * Retrieve user welcome info
     *
     * @return mixed|string
     */
    public function getUserWelcomeInfo()
    {
        try {
            $data = $this->welcomeInfoRepository->getUserWelcomeInfo();
            return $this->sendSuccessResponse($data, 'User Welcome Info');
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }
}
