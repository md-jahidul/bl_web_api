<?php
namespace App\Http\Controllers\API\V1;


use App\Enums\HttpStatusCode;
use App\Exceptions\BLServiceException;
use App\Exceptions\CurlRequestException;
use App\Exceptions\RequestUnauthorizedException;
use App\Http\Controllers\Controller;
use App\Http\Requests\AuthTokenRequest;
use App\Http\Requests\ForgetPasswordRequest;
use App\Http\Requests\LoginWithOtpRequest;
use App\Http\Requests\OtpLoginRequest;
use App\Http\Requests\PasswordChangeRequest;
use App\Http\Requests\SetPasswordRequest;
use App\Services\ApiBaseService;
use App\Services\NumberValidationService;
use App\Services\SecreteTokenService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
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
     * @var SecreteTokenService
     */
    private $secreteTokenService;

    /**
     * AuthenticationController constructor.
     * @param UserService $userService
     * @param NumberValidationService $numberValidationService
     */
    public function __construct(
        UserService $userService,
        NumberValidationService $numberValidationService,
        SecreteTokenService $secreteTokenService,
        ApiBaseService $apiBaseService
    ) {
        $this->userService = $userService;
        $this->numberValidationService = $numberValidationService;
        $this->secreteTokenService = $secreteTokenService;
        $this->apiBaseService = $apiBaseService;
    }

    /**
     * @param Request $request
     * @param $mobile
     * @return JsonResponse
     * @throws RequestUnauthorizedException
     */
    public function numberValidation(Request $request, $mobile): JsonResponse
    {
        return $this->numberValidationService->validateNumberWithResponse($mobile, $validateReq = true);
    }

    /**
     * @param Request $request
     * @return JsonResponse|string
     */
    public function requestOtpLogin(Request $request)
    {
        return $this->userService->otpLoginRequest($request);
    }

    /**
     * @param Request $request
     * @return JsonResponse|mixed
     */
    public function otpLogin(OtpLoginRequest $request)
    {
        return $this->userService->otpLogin($request);
    }

    public function passwordLogin(AuthTokenRequest $request)
    {
        $data = $request->input();
        $response = $this->userService->getAuthToken($data);
        $statusCode = $response['status_code'];
        $responseData = $response['data'];

        if (isset($responseData['error'])) {
            return $this->apiBaseService->sendErrorResponse($responseData['message'], "Incorrect Password", HttpStatusCode::UNAUTHORIZED);
        }

        return $this->apiBaseService->sendSuccessResponse($responseData, 'Successful Attempt');
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

    /**
     * @param SetPasswordRequest $request
     * @return JsonResponse
     * @throws \App\Exceptions\TokenInvalidException
     * @throws \App\Exceptions\TokenNotFoundException
     */
    public function setPassword(SetPasswordRequest $request)
    {
        return $this->userService->setPassword($request);
    }

    /**
     * Update Password
     *
     * @param Request $request
     * @return JsonResponse
     * @throws \App\Exceptions\TokenInvalidException
     */
    public function forgetPassword(ForgetPasswordRequest $request)
    {
        return $this->userService->forgetPassword($request);
    }

    /**
     * Update Password
     *
     * @param PasswordChangeRequest $request
     * @return JsonResponse
     * @throws CurlRequestException
     * @throws \App\Exceptions\OldPasswordMismatchException
     * @throws \App\Exceptions\TokenInvalidException
     * @throws \App\Exceptions\TokenNotFoundException
     * @throws \App\Exceptions\TooManyRequestException
     */
    public function changePassword(PasswordChangeRequest $request)
    {
        return $this->userService->changePassword($request);
    }


    /**
     * @param LoginWithOtpRequest $request
     * @return JsonResponse
     * @throws BLServiceException
     * @throws CurlRequestException
     */
    public function verifyOTPForLogin(LoginWithOtpRequest $request)
    {
        return $this->userService->verifyOTPForLogin($request);
    }
}
