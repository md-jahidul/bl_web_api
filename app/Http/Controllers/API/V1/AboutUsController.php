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
     * @param Request $request
     * @return JsonResponse
     * @throws IdpAuthException
     */
    public function getAboutBanglalink(Request $request)
    {
        return $this->aboutUsService->getAboutBanglalink($request);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @throws IdpAuthException
     */
    public function getAboutManagement(Request $request)
    {
        return $this->aboutUsService->getAboutManagement($request);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws IdpAuthException
     */
    public function getEcareersInfo(Request $request)
    {
        return $this->aboutUsService->getEcareersInfo($request);
    }


}
