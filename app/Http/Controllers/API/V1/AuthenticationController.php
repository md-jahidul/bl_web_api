<?php
/**
 * Created by PhpStorm.
 * User: jahangir
 * Date: 11/24/19
 * Time: 11:54 AM
 */

namespace App\Http\Controllers\API\V1;


use App\Enums\HttpStatusCode;
use App\Http\Controllers\Controller;
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
     * AuthenticationController constructor.
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }


    public function requestOtpLogin(Request $request)
    {
        return $this->userService->otpLoginRequest($request);
    }

    public function otpLogin(Request $request)
    {
        $validator = Validator::make($request->all(), $this->getLoginValidationRules());
        if ($validator->fails()) {
            return response()->json($validator->messages(), HttpStatusCode::VALIDATION_ERROR);
        }

        return $this->userService->otpLogin($request);
    }

    private function getLoginValidationRules()
    {
        return ['mobile' => 'required', 'otp_session' => 'required'];
    }

    private function getRequestLoginValidationRules()
    {
        return ['mobile' => 'required'];
    }
}