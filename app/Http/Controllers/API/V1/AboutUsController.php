<?php

namespace App\Http\Controllers\API\V1;

use App\Exceptions\IdpAuthException;
use App\Http\Controllers\Controller;
use App\Services\AboutUsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class AboutUsController extends Controller
{

    /**
     * @var AboutUsService
     */
    protected $aboutUsService;


    /**
     * AboutUsController constructor.
     * @param AboutUsService $aboutUsService
     */
    public function __construct(AboutUsService $aboutUsService)
    {
        $this->aboutUsService = $aboutUsService;
    }

    /**
     * @return JsonResponse
     */
    public function getAboutBanglalink()
    {
        return $this->aboutUsService->getAboutBanglalink();
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getAboutManagement()
    {
        return $this->aboutUsService->getAboutManagement();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws IdpAuthException
     */
    public function getEcareersInfo()
    {
        return $this->aboutUsService->getEcareersInfo();
    }


}
