<?php

namespace App\Http\Controllers\API\V1;

use App\Services\LoginLandingPageService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginLandingPageController extends Controller
{

    /**
     * @var LoginLandingPageService
     */
    protected $loginLandingPageService;

    /**
     * LoginLandingPageController constructor.
     * @param LoginLandingPageService $LoginLandingPageService
     */
    public function __construct(LoginLandingPageService $loginLandingPageService)
    {
        $this->loginLandingPageService = $loginLandingPageService;
    }

    public function getBanner(Request $request)
    {
        return $this->loginLandingPageService->getLoginPageBanner();
    }

}
