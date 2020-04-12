<?php

namespace App\Services;

use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use App\Repositories\OtpConfigRepository;
use Carbon\Carbon;
use App\Enums\HttpStatusCode;
use App\Http\Requests\CustomerRequest;
use App\Models\Otp;
use App\Repositories\CustomerRepository;
use App\Repositories\OtpRepository;
use JWTAuth;
use Illuminate\Support\Facades\Crypt;
use App\Services\Banglalink\BanglalinkOtpService;
use App\Services\Banglalink\BanglalinkCustomerService;


/**
 * Class RegistrationService
 * @package App\Services
 */
class RegistrationService extends ApiBaseService
{

    /**
     * @var CustomerRepository
     */
    protected $customerRepository;

    /**
     * @var OtpRepository
     */
    protected $otpRepository;

    /**
     * @var BanglalinkOtpService
     */
    protected $blOtpService;

    /**
     * @var OtpConfigRepository
     */
    protected $otpConfigRepository;

    /**
     * @var BanglalinkCustomerService
     */
    protected $blCustomerService;


    /**
     * RegistrationService constructor.
     * @param CustomerRepository $customerRepository
     * @param OtpRepository $otpRepository
     * @param BanglalinkOtpService $otpService
     * @param OtpConfigRepository $otpConfigRepository
     */
    public function __construct(CustomerRepository $customerRepository, OtpRepository $otpRepository,
        BanglalinkOtpService $blOtpService, OtpConfigRepository $otpConfigRepository, BanglalinkCustomerService $blCustomerService)
    {
        $this->customerRepository = $customerRepository;
        $this->otpRepository = $otpRepository;
        $this->blOtpService = $blOtpService;
        $this->otpConfigRepository = $otpConfigRepository;
        $this->blCustomerService = $blCustomerService;
    }

    /**
     * Validate number
     *
     * @param $number
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateNumber($number)
    {
        $missdn = "88" . $number;

        $customer = $this->blCustomerService->getCustomerInfoByNumber($missdn);


        if ($customer->getData()->status == "FAIL") {
            return $this->sendErrorResponse(
                "Something went wrong",
                [],
                HttpStatusCode::INTERNAL_ERROR
            );
        }

        if ($customer->getData()->data->status == "active") {
            return $this->sendSuccessResponse(
                [],
                "Number is Valid",
                [],
                HttpStatusCode::SUCCESS
            );
        } else {
            return $this->sendErrorResponse(
                "Number is Not Valid",
                [],
                HttpStatusCode::VALIDATION_ERROR
            );
        }
    }


    /**
     * Register user
     *
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function registerUser(CustomerRequest $request)
    {
        $data = $request->all();

        $data['mobile'] =  $request->input('phone');

        $data['password_confirmation'] =  $request->input('password');

        $data['username'] =  $request->input('phone');

        $data['grant_type'] = "password";

        $data['provider'] = "users";

        $number = $request->input('phone');

        $mobile = "88" . $number;

        $customer_info = $this->blCustomerService->getCustomerInfoByNumber($mobile);

        if ($customer_info->getData()->status == "FAIL") {
            return $this->sendErrorResponse("User is not Banglalink valid user", [], HttpStatusCode::NOT_FOUND);
        }

        $customer_account_id = $customer_info->getData()->data->package->customerId;

        $data['customer_account_id'] = $customer_account_id;

        $response = IdpIntegrationService::registrationRequest($data);

        $message_response = "something went wrong";
        if (!$response) {
            return $this->sendErrorResponse($message_response, [], HttpStatusCode::INTERNAL_ERROR);
        }

        if (isset(json_decode($response)->status) == "Success") {
            $this->customerRepository->create($data);
            $token = IdpIntegrationService::loginRequest($data);
            $response_customer = IdpIntegrationService::getCustomerInfo($number);

            if (isset(json_decode($response_customer)->status) == "Success") {
                $customer = json_decode($response_customer)->data;
            } else {
                $customer = null;
            }

            $final_data = [
                'token' => json_decode($token),
                'customer' => $customer,
            ];

            return $this->sendSuccessResponse($final_data, 'Registration', [], HttpStatusCode::SUCCESS);
        }

        return $this->sendErrorResponse(json_decode($response), [], HttpStatusCode::INTERNAL_ERROR);
    }


    /**
     * Login via Idp
     *
     * @param $request
     * @return string
     */
    public function login($request)
    {

        $number = $request->input('username');

        $login_response = IdpIntegrationService::loginRequest($request->all());

        $login_response = json_decode($login_response, true);

        if (isset($login_response['error'])) {
            return $this->sendErrorResponse(
                $login_response['message'],
                [],
                HttpStatusCode::UNAUTHORIZED
            );
        }

        $response = IdpIntegrationService::getCustomerInfo($number);

        $data = json_decode($response, true);

        if (isset($data['status'])) {
            $user = Customer::where('phone', $data['data']['mobile'])->first();

            if (!$user) {
                return $this->sendErrorResponse("User Credentials Invalid", [], HttpStatusCode::UNAUTHORIZED);
            }

            $customer = new CustomerResource($user);
        } else {
            $customer = null;
        }

        $final_data = [
            'token' => $login_response,
            'customer' => $customer,
        ];

        return $this->sendSuccessResponse(
            $final_data,
            "Login",
            [],
            HttpStatusCode::SUCCESS
        );
    }



    /**
     * Send OTP
     *
     * @param $number
     * @return string
     */
    public function sendOTP($number)
    {

        $otp_config = $this->otpConfigRepository->getOtpConfig();

        $conf = $otp_config->toArray();

        if (isset($conf[0]['validation_time'])) {
            $validation_time = $conf[0]['validation_time'];
            $otp_bl = $this->blOtpService->sendOtp($number, $conf[0]['token_length_string'], "#", $validation_time);
        } else {
            $validation_time = config('apiconfig.opt_token_expiry');
            $otp_bl = $this->blOtpService->sendOtp($number);
        }


        $token = $this->generateOtpToken(18);

        $encrypted_token = Crypt::encryptString($token);

        $otp = $this->generateNumericOTP(6);

        $this->otpRepository->createOtp($number, $otp, $encrypted_token);

        $data = [
            'validation_time' => $validation_time,
            'otp_token' => $encrypted_token
        ];

        return $this->sendSuccessResponse($data, 'OTP Send Successfully', [], HttpStatusCode::SUCCESS);
    }


    /**
     * Verify OTP
     *
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyOTP($request)
    {
        $otp = $request->input('otp');

        $number = $request->input('username');

        $result = $this->blOtpService->validateOtp($number, $otp);


        $final_data = null;

        if ($request->input('request_type') != 'forgot_password') {
            $final_data = $this->getCustomerInfoWithToken($request, $number);
        }

        if ($result['status_code'] == 200) {
            return $this->sendSuccessResponse(
                $final_data,
                "OTP is valid!",
                [],
                HttpStatusCode::SUCCESS
            );
        }

        return $this->sendErrorResponse(
            "OTP is not valid",
            [],
            HttpStatusCode::INTERNAL_ERROR
        );
    }


    /**
     * Generate jwt token for otp
     *
     * @param $phone
     * @return mixed
     */
    public function getToken($phone)
    {
        $otpInfo = $this->otpRepository->getOtpInfo($phone);

        if (!$otpInfo) {
            $otp = Otp::create(['phone' => $phone]);
        } else {
            $otp = $otpInfo;
        }

        $token = JWTAuth::fromUser($otp);

        return $token;
    }


    /**
     * @param $request
     * @param $number
     * @return array
     */
    public function getCustomerInfoWithToken($request, $number)
    {
        $response = IdpIntegrationService::getCustomerInfo($number);

        $data = json_decode($response, true);

        if (isset($data['status'])) {
            $user = Customer::where('phone', $data['data']['mobile'])->first();

            if (!$user) {
                return $this->sendErrorResponse("User Credentials Invalid", [], HttpStatusCode::UNAUTHORIZED);
            }

            $customer = new CustomerResource($user);
        } else {
            $customer = null;
        }

        $data = $request->all();

        $data['otp'] = "1234";

        $tokenResponse = IdpIntegrationService::otpGrantTokenRequest($data);

        $final_data = [
            'token' => json_decode($token),
            'customer' => $customer,
        ];
        return $final_data;
    }


    /**
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgetPassword($request)
    {

        $data = $request->all();

        $data['provider'] = "users";

        $data['otp'] = "1234";

        $data['mobile'] =  $request->input('phone');

        $data['password_confirmation'] =  $request->input('password');

        $response = IdpIntegrationService::forgetPasswordRequest($data);


        if (app('Illuminate\Http\Response')->status() == 200) {
            return $this->sendSuccessResponse(
                json_decode($response),
                "Password updated successfully!",
                [],
                HttpStatusCode::SUCCESS
            );
        }

        return $this->sendErrorResponse(
            "Failed!Please try again",
            [],
            HttpStatusCode::INTERNAL_ERROR
        );
    }


    /**
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword($request)
    {

        $data = $request->all();

        $data['mobile'] =  $request->input('phone');

        $response = IdpIntegrationService::changePasswordRequest($data);

        $result = json_decode($response, true);


        if (isset($result['error'])) {
            return $this->sendErrorResponse(
                "Old password is not correct",
                [],
                HttpStatusCode::VALIDATION_ERROR
            );
        }

        if (app('Illuminate\Http\Response')->status() == 200) {
            return $this->sendSuccessResponse(
                [],
                "Password updated successfully!",
                [],
                HttpStatusCode::SUCCESS
            );
        }

        return $this->sendErrorResponse(
            "Failed!Please try again",
            [],
            HttpStatusCode::INTERNAL_ERROR
        );
    }


    /**
     * Generate OTP
     *
     * @param $n
     * @return string
     */
    public function generateNumericOTP($n)
    {
        $generator = "1357902468";

        $result = "";

        for ($i = 1; $i <= $n; $i++) {
            $result .= substr($generator, (rand() % (strlen($generator))), 1);
        }

        return $result;
    }


    public function generateOtpToken($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }
}
