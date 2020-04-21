<?php
namespace App\Http\Controllers\API\V1;


use App\Enums\HttpStatusCode;
use App\Http\Controllers\Controller;
use App\Http\Requests\OtpLoginRequest;
use App\Services\NumberValidationService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthenticationController extends Controller
{
    /**
     * @var UserService $userService
     */
    protected $userService;

    /**
     * @var NumberValidationService
     */
    protected $numberValidationService;

    /**
     * AuthenticationController constructor.
     * @param UserService $userService
     * @param NumberValidationService $numberValidationService
     */
    public function __construct(UserService $userService, NumberValidationService $numberValidationService)
    {
        $this->userService = $userService;
        $this->numberValidationService = $numberValidationService;
    }

    /**
     * @param $mobile
     * @return \Illuminate\Http\JsonResponse
     */
    public function numberValidation($mobile)
    {
        return $this->numberValidationService->validateNumberWithResponse($mobile);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function requestOtpLogin(Request $request)
    {
        return $this->userService->otpLoginRequest($request);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function otpLogin(OtpLoginRequest $request)
    {
        return $this->userService->otpLogin($request);
    }

    private function getLoginValidationRules()
    {
        return ['mobile' => 'required', 'otp_session' => 'required', 'otp' => 'required'];
    }

    private function getRequestLoginValidationRules()
    {
        return ['mobile' => 'required'];
    }


    public function getRefreshToken(Request $request){

        return $this->userService->getRefreshToken($request);

    }

}
