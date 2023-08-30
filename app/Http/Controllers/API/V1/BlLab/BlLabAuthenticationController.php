<?php

namespace App\Http\Controllers\API\V1\BlLab;

use App\Exceptions\IdpAuthException;
use App\Http\Controllers\Controller;
use App\Http\Requests\BlLabRegisterRequest;
use App\Http\Requests\BlLabVerifyOTPRequest;
use App\Services\AboutUsService;
use App\Services\BlLabs\BlLabsAuthenticationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class BlLabAuthenticationController extends Controller
{
    /**
     * @var BlLabsAuthenticationService
     */
    private $blLabsUserService;

    /**
     * BlLabAuthenticationController constructor.
     * @param BlLabsAuthenticationService $blLabsUserService
     */
    public function __construct(BlLabsAuthenticationService $blLabsUserService)
    {
        $this->blLabsUserService = $blLabsUserService;
    }

    public function login(Request $request)
    {
        $data = $request->only('email', 'password');
        return $this->blLabsUserService->login($data);
    }

    /**
     * @return JsonResponse
     */
    public function sendOTP(Request $request)
    {
        return $this->blLabsUserService->sendOTP($request);
    }

    /**
     * @return JsonResponse
     */
    public function verifyOTP(BlLabVerifyOTPRequest $request)
    {
        return $this->blLabsUserService->verifyOTP($request);
    }

    /**
     * @return JsonResponse
     */
    public function register(BlLabRegisterRequest $request): JsonResponse
    {
        return $this->blLabsUserService->register($request);
    }

    /**
     * Refresh a token.
     *
     * @return array
     */
    public function refresh()
    {
        return $this->blLabsUserService->refreshToken();
    }

    public function forgetPassword(Request $request)
    {
        return $this->blLabsUserService->forgetPassword($request);
    }

    public function profile()
    {
        return "Success";
    }
}