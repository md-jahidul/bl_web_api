<?php

namespace App\Http\Controllers\API\V1\BlLab;

use App\Exceptions\IdpAuthException;
use App\Http\Controllers\Controller;
use App\Http\Requests\BlLabRegisterRequest;
use App\Http\Requests\BlLabVerifyOTPRequest;
use App\Services\AboutUsService;
use App\Services\BlLabs\BlLabsUserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class BlLabUserController extends Controller
{
    /**
     * @var BlLabsUserService
     */
    private $blLabsUserService;

    /**
     * BlLabUserController constructor.
     * @param BlLabsUserService $blLabsUserService
     */
    public function __construct(BlLabsUserService $blLabsUserService)
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
    public function register(BlLabRegisterRequest $request)
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
