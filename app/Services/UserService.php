<?php

namespace App\Services;

use App\Enums\HttpStatusCode;
use App\Exceptions\BLApiHubException;
use App\Exceptions\BLServiceException;
use App\Exceptions\CurlRequestException;
use App\Exceptions\TokenInvalidException;
use App\Exceptions\TokenNotFoundException;
use App\Jobs\ProcessHibernateCustomerLoginBonus;
use App\Models\Customer;
use App\Repositories\CustomerRepository;
use App\Repositories\OtpConfigRepository;
use App\Repositories\OtpRepository;
use App\Services\Banglalink\BalanceService;
use App\Services\Banglalink\BanglalinkCustomerService;
use App\Services\Banglalink\BanglalinkOtpService;
use App\Repositories\UserRepository;
use App\Http\Requests\DeviceTokenRequest;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Generator\RandomBytesGenerator;

/**
 * Class UserService
 * @package App\Services
 */
class UserService extends ApiBaseService
{

    /**
     * @var UserRepository
     */
    protected $userRepository;
    /**
     * @var NumberValidationService
     */
    protected $numberValidationService;

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

    /*
     * @var BalanceService
     */
    protected $balanceService;

    /**
     * @var CustomerService
     */
    protected $customerService;
    /**
     * @var CustomerService
     */
    private $blCustomerService;
    /**
     * @var LogService
     */
    private $logService;
    /**
     * @var CustomerRepository
     */
    private $customerRepository;


    /**
     * UserService constructor.
     *
     * @param UserRepository $userRepository
     * @param NumberValidationService $numberValidationService
     * @param OtpRepository $otpRepository
     * @param BanglalinkOtpService $blOtpService
     * @param OtpConfigRepository $otpConfigRepository
     */
    public function __construct(
        UserRepository $userRepository,
        NumberValidationService $numberValidationService,
        OtpRepository $otpRepository,
        BanglalinkOtpService $blOtpService,
        OtpConfigRepository $otpConfigRepository,
        BalanceService $balanceService,
        CustomerService $customerService,
        BanglalinkCustomerService $banglalinkCustomerService,
        LogService $logService,
        CustomerRepository $customerRepository
    ) {
        $this->userRepository = $userRepository;
        $this->numberValidationService = $numberValidationService;
        $this->otpRepository = $otpRepository;
        $this->blOtpService = $blOtpService;
        $this->otpConfigRepository = $otpConfigRepository;
        $this->balanceService = $balanceService;
        $this->customerService = $customerService;
        $this->blCustomerService = $banglalinkCustomerService;
        $this->logService = $logService;
        $this->customerRepository = $customerRepository;
    }


    /**
     * @param $request
     * @return \Illuminate\Http\JsonResponse|string
     * @throws BLApiHubException
     */
    public function otpLoginRequest($request)
    {
        $mobile = $request->mobile;
        $validationResponse = $this->numberValidationService->validateNumberWithResponse($mobile);

        if ($validationResponse->getData()->status == 'FAIL') {
            return $validationResponse;
        }

        if (!$this->isUserExist($mobile)) {
            $customerInfo = $validationResponse->getData()->data;
            $registrationStatus = $this->register($customerInfo, $mobile);
            if ($registrationStatus['status'] == 'FAIL') {
                return $this->sendErrorResponse($registrationStatus['data']->message, [], $registrationStatus['status_code']);
            }
        }

        return $this->sendOTP($mobile);

    }

    /**
     * @param $request
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function otpLogin($request)
    {
        //TODO: Done Check otp session with database
        $getOtpInfo = $this->otpRepository->validateOtpToken($request['mobile'], $request['otp_session']);

        if( empty($getOtpInfo) ){
            return $this->sendErrorResponse('Token is Invalid or Expired', [],
                HttpStatusCode::UNAUTHORIZED);
        }

        $data['otp'] = $request['otp'];
        $data['grant_type'] = "otp_grant";
        $data['client_id'] = config('apiurl.idp_otp_client_id');
        $data['client_secret'] = config('apiurl.idp_otp_client_secret');
        $data['username'] = $request['mobile'];

        $tokenResponse = IdpIntegrationService::otpGrantTokenRequest($data);

        $tokenResponseData = json_decode($tokenResponse['data']);


        if ($tokenResponse['http_code'] != 200) {
            return $this->sendErrorResponse( 'Something went wrong. Try again later',
                ['message' => "Something went wrong. Try again later"],
                HttpStatusCode::BAD_REQUEST
            );

        } else {

            $idpCus = IdpIntegrationService::getCustomerBasicInfo($request['mobile']);

            $customerInfo = $this->getCustomerBasicInfo($request['mobile'], (object)$idpCus);

            $profileData = [
                'token' => $tokenResponseData,
                'customerInfo' => $customerInfo,
            ];

            return $this->sendSuccessResponse($profileData, "Data found");
        }
    }


    /**
     * @param $mobile
     * @param null $idpUserData
     * @return array|null
     */
    public function getCustomerBasicInfo($mobile, $idpUserData = null)
    {
        $customerInfo = array();

        $user = $this->userRepository->findOneBy(['phone' => $mobile]);
        if (!$user)  return null;

        $user_data = [];
        if( !empty($idpUserData) ){

            if(isset($idpUserData->data) ){
                $idpUserData = json_decode($idpUserData->data);
                $idpUserData = $idpUserData->data ?? null;
            }
            else{
                $idpUserData = json_decode($idpUserData);
            }


            $user_data["id"] =  $user->id ?? null;
            $user_data["phone"] = $user->phone ?? null;
            $user_data["customer_account_id"] =  $user->customer_account_id ?? null;
            $user_data["name"] =  $idpUserData->name ?? null;
            $user_data["email"] =  $idpUserData->email ?? null;
            $user_data["msisdn"] =  $idpUserData->msisdn ?? null;
            $user_data["birth_date"] =  $idpUserData->birth_date ?? null;
            $user_data["first_name"] = $idpUserData->first_name ?? null;
            $user_data["last_name"] =  $idpUserData->last_name ?? null;
            $user_data["gender"] =  $idpUserData->gender ?? null;
            $user_data["alternate_phone"] =  $idpUserData->alternate_phone ?? null;
            $user_data["mobile"] =  $idpUserData->mobile ?? null;

        }
        else{
            $user_data = $user;
        }

        $customerInfo['personal_data'] = $user_data;

       // $balanceData = $this->balanceService->getBalanceSummary($user_data['phone']);
       // $customerInfo['balance_data'] = $balanceData['status'] == 'SUCCESS' ? $balanceData['data'] : $balanceData;

        return $customerInfo;

    }


    /**
     * Send OTP
     *
     * @param $number
     * @return string
     * @throws BLApiHubException
     */
    public function sendOTP($number)
    {
        $otp_bl = $this->blOtpService->sendOtp($number);

        if ($otp_bl['status_code'] != 202) {
            throw new BLApiHubException('Cannot send otp');
        }

        $token = $this->generateOtpToken(18);

        $encrypted_token = Crypt::encryptString($token);

        $otp = null;

        $this->otpRepository->createOtp($number, $otp, $encrypted_token);

        $data = [
            'validation_time' => config('apiconfig.opt_token_expiry'),
            'otp_token' => $encrypted_token
        ];

        return $this->sendSuccessResponse($data, 'OTP Send Successfully',
            [], HttpStatusCode::SUCCESS);
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


    /**
     * @param int $length
     * @return string
     */
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


    /**
     * @param $request
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function viewProfile($request)
    {
        $bearerToken = ['token' => $request->header('authorization')];
        $response = IdpIntegrationService::tokenValidationRequest($bearerToken);

        $idpData = json_decode($response['data']);

        if ($response['http_code'] != 200 || $idpData->token_status != 'Valid') {
            return $this->sendErrorResponse("Token is Invalid", [], HttpStatusCode::UNAUTHORIZED);
        }

        $idpUser = $idpData->user;

        $user = $this->getCustomerDetails($idpData->user->mobile, json_encode($idpUser));

        return $this->sendSuccessResponse($user, 'Data found', []);
    }


    /**
     * Get Customer Details
     *
     * @param $mobile
     * @param null $idpUserData
     * @return array|null
     */
    public function getCustomerDetails($mobile, $idpUserData = null)
    {
        $customerInfo = array();

        $user = $this->userRepository->findOneBy(['phone' => $mobile]);
        if (!$user)
            return null;

        $user_data = [];
        if( !empty($idpUserData) ){

            if( isset($idpUserData->data) ){
                $idpUserData = json_decode($idpUserData->data);
                $idpUserData = $idpUserData->data;
            }
            else{
                $idpUserData = json_decode($idpUserData);
            }

            $user_data["id"] =  $user->id ?? null;
            $user_data["phone"] = $user->phone ?? null;
            $user_data["customer_account_id"] =  $user->customer_account_id ?? null;
            $user_data["name"] =  $idpUserData->name ?? null;
            $user_data["email"] =  $idpUserData->email ?? null;
            $user_data["msisdn"] =  $idpUserData->msisdn ?? null;
            $user_data["birth_date"] =  $idpUserData->birth_date ?? null;
            $user_data["profile_image"] =  $idpUserData->profile_image ?? null;
            $user_data["first_name"] = $idpUserData->first_name ?? null;
            $user_data["last_name"] =  $idpUserData->last_name ?? null;
            $user_data["gender"] =  $idpUserData->gender ?? null;
            $user_data["alternate_phone"] =  $idpUserData->alternate_phone ?? null;
            $user_data["mobile"] =  $idpUserData->mobile ?? null;
            $user_data["address"] =  $idpUserData->address ?? null;
            $user_data["district"] =  $user->district ?? null;
            $user_data["thana"] =  $user->thana ?? null;
        }
        else {
            $user_data = $user;
        }

        $customerInfo['personal_data'] = $user_data;

        $balanceData = $this->balanceService->getBalanceSummary($user_data['phone']);
        $customerInfo['balance_data'] = $balanceData['status'] == 'SUCCESS' ? $balanceData['data'] : $balanceData;

        return $customerInfo;

    }

    /**
     * @param $mobile
     * @return bool
     */
    public function isUserExist($mobile)
    {
        $user = $this->userRepository->findOneBy(['phone' => $mobile]);

        return $user ? true : false;
    }

    /**
     * @param $customerInfo
     * @param $mobile
     * @return array
     */
    private function register($customerInfo, $mobile)
    {
        $data['mobile'] = $mobile;
        $data['phone'] = $mobile;
        $data['msisdn'] = '88' . $mobile;
        $randomPass = $this->generateRandomString();
        $data['password'] = $randomPass;
        $data['password_confirmation'] = $randomPass;

        # is password_added
        //$data['is_password_set'] = 1;

        $data['username'] = $mobile;

        $customer_account_id = $customerInfo->package->customerId;

        $data['customer_account_id'] = $customer_account_id;

        $idpCus = IdpIntegrationService::getCustomerInfo($mobile);

        if ($idpCus['http_code'] != 200) {
            //If customer is not exist add data to IDP
            $response = IdpIntegrationService::registrationRequest($data);
            if ($response['http_code'] != 201) {
                $errorData = json_decode($response['data']);
                return ['status' => 'FAIL', 'data' => $errorData, 'status_code' => $response['http_code']];
            }
        }

        $data['platform'] = 'assetlite';
        $user = $this->userRepository->create($data);

        return ['status' => 'SUCCESS', 'data' => $user];

    }

    public function updateProfile($request)
    {
        $bearerToken = ['token' => $request->header('authorization')];

        $response = IdpIntegrationService::tokenValidationRequest($bearerToken);
        $idpData = json_decode($response['data']);

        if ($response['http_code'] != 200 || $idpData->token_status != 'Valid') {
            return $this->sendErrorResponse("Token is Invalid", [], HttpStatusCode::UNAUTHORIZED);
        }

        #update data to ID
        $path = null;
        // Log::info(print_r($path));
        // Log::info('eeeee');

        if ($request->hasFile('profile_photo')){
            $path = $this->uploadImage($request);

            $update_data [] = [
                'Content-type' => 'multipart/form-data',
                'name' => 'profile_photo',
                'contents' => fopen(storage_path('app/public/' . $path), 'r')
            ];
        }


        $requested_input = ['name', 'email', 'first_name', 'last_name', 'birth_date', 'gender', 'alternate_phone', 'address' ];

        foreach ($request->all() as $request_key => $request_value) {

            if(  in_array($request_key, $requested_input) ){
                $update_data [] = [
                    'name' => $request_key,
                    'contents' => ($request->filled($request_key)) ? $request->input($request_key) : null
                ];
            }


        }

        $client = new Client();
        $response = $client->post(
            config('apiurl.idp_host') . '/api/v1/customers/update/perform',
            [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => $request->header('authorization'),
                ],
                'multipart' => $update_data,
            ]
        );

        if ($response->getStatusCode() != HttpStatusCode::SUCCESS) {
            return $this->sendErrorResponse("Cannot update profile. try again later", [], $response->getStatusCode());
        }
        $response = json_decode($response->getBody()->getContents(), true);


        if ( $request->hasFile('profile_photo') && isset($path) ){
        		try {
        		    if ($path) {
        		        unlink(storage_path('app/public/' . $path));
        		    }
        		} catch (Exception $e) {
        		    Log::error('Error in saving profile photo');
        		}
        }



        # update customer table
        $user = $this->userRepository->findOneBy(['phone' => $idpData->user->mobile]);
        if (!$user) {
            return $this->sendErrorResponse('User not found in the system');
        }
        $data = $request->all();
        $data['msisdn'] = '88' . $idpData->user->mobile;

        if ($request->hasFile('profile_photo')) {

            $data['profile_image'] = isset($path) ? $path : null;
        }

        $user->update($data);

        return $this->sendSuccessResponse($user, 'Data updated successfully');
    }

    public function uploadProfileImage($request)
    {
        $path = $this->uploadImage($request);

        $update_data [] = [
            'Content-type' => 'multipart/form-data',
            'name' => 'profile_photo',
            'contents' => fopen(storage_path('app/public/' . $path), 'r')
        ];

        $client = new Client();
        $response = $client->post(
            config('apiurl.idp_host') . '/api/v1/customers/profile/photo/set',
            [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => $request->header('authorization'),
                ],
                'multipart' => $update_data
            ]
        );

        if ($response->getStatusCode() != HttpStatusCode::SUCCESS) {
            return $this->sendErrorResponse("Cannot update profile. try again later", [], $response->getStatusCode());
        }
        $response = json_decode($response->getBody()->getContents(), true);
        try {
            if ($path) {
                unlink(storage_path('app/public/' . $path));
            }
        } catch (Exception $e) {
            Log::error('Error in saving profile photo');
        }

        return $this->sendSuccessResponse(['image_path' => $response['data']['image_path']], 'Profile picture updated successfully');
    }

    private function uploadImage($request)
    {
        try {
            $file = $request->file('profile_photo');
            $ext = $file->getClientOriginalExtension();
            $photoExt = array('jpg', 'JPG', 'JPEG', 'jpeg', 'png', 'PNG', 'gif', 'bmp');
            if (!in_array($ext, $photoExt)) {
                return $this->sendErrorResponse('Invalid image extension', [], 400);
            }
            $fileName = md5(strtotime(now())) . '.' . $file->getClientOriginalExtension();
            $file->storeAs(
                'uploads/profile-images',
                $fileName,
                'public'
            );
            return 'uploads/profile-images/' . $fileName;
        } catch (\Exception $e) {
            return $this->sendErrorResponse($e->getMessage(), [], 500);
        }
    }

    public function removeProfileImage(Request $request)
    {
        $client = new Client();
        $response = $client->get(
            config('apiurl.idp_host') . '/api/v1/customers/profile/photo/remove',
            [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => $request->header('authorization'),
                ],
            ]
        );
        if ($response->getStatusCode() == HttpStatusCode::SUCCESS)
            return $this->sendSuccessResponse([], 'Profile image removed successfully');

        return $this->sendErrorResponse('Cannot remove photo', [], $response->getStatusCode());

    }

    public function generateRandomString($length = 8)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function getAuthToken($data)
    {
        $response = IdpIntegrationService::loginRequest($data);

        return [
            'status_code' => $response['http_code'],
            'data' => json_decode($response['data'], true)
        ];
    }

    public function getRefreshToken($request)
    {
        $bearerToken = $request->bearerToken();
        // $request = $request->all();

        $data['grant_type'] = "refresh_token";
        $data['client_id'] = $request->input('client_id') ?? config('apiurl.idp_otp_client_id');
        $data['client_secret'] = $request->input('client_secret') ?? config('apiurl.idp_otp_client_secret');
        $data['refresh_token'] = $request->input('refresh_token');
        $data['bearere_token'] = $bearerToken;

        $tokenResponse = IdpIntegrationService::otpRefreshTokenRequest($data);

        $tokenResponseData = json_decode($tokenResponse['data']);

        if ($tokenResponse['http_code'] != 200) {
            return $this->sendErrorResponse('IDP error', $tokenResponseData->message, HttpStatusCode::UNAUTHORIZED);
        }
        else {
           // $idpCus = IdpIntegrationService::getCustomerInfo($request['mobile']);

           // $customerInfo = $this->getCustomerInfo($request['mobile'], (object)$idpCus);

        //    $profileData = [
        //        'token' => $tokenResponseData,
        //        // 'customerInfo' => $customerInfo,
        //    ];

           return $this->sendSuccessResponse($tokenResponseData, "Successful Attempt");
        }




        // if (isset($response['error'])) {
        //     return $this->sendErrorResponse(
        //         $response['message'],
        //         [
        //             'message' => "The refresh token is invalid."
        //         ],
        //         HttpStatusCode::UNAUTHORIZED
        //     );
        // }

        // return $this->sendSuccessResponse(
        //     json_decode($token['response']),
        //     "Refresh Token",
        //     [],
        //     HttpStatusCode::SUCCESS
        // );
    }

    public function setPassword(Request $request)
    {
        if (!$request->bearerToken()) {
            throw new TokenNotFoundException();
        }

        // validate the token and get details info
        $bearerToken = ['token' => $request->header('authorization')];

        $result = IdpIntegrationService::tokenValidationRequest($bearerToken);

        $data = json_decode($result['data'], true);

        if ($data['token_status'] != 'Valid') {
            throw new TokenInvalidException();
        }

        $msisdn_key = 'mobile';

        $customer = Customer::where('phone', $data['user'][$msisdn_key])->first();

        if (!$customer) {
            throw new TokenInvalidException();
        }

        // validate otp token first
        /* $token_exist = Otp::where('phone', $customer->phone)->first();

         if (!($token_exist && $request->otp_token == $token_exist->token)) {
             return $this->sendErrorResponse("OTP token is invalid", [
                 'message' => "OTP token is invalid",
                 'hint' => 'OTP token is invalid',
                 'target' => 'query'
             ], 400);
         }

         $token_exist->delete();*/

        $idp_customer_info = $data['user'];

        if ($idp_customer_info['is_password_set']) {
            return $this->sendErrorResponse("Password is already set", [
                'message' => "Password is already set",
                'hint' => 'Password is already set',
                'target' => 'query'
            ], 400);
        }

        $client = new Client();

        $data['otp'] = $request->otp;
        $data['password'] = $request->password;

        try {
            $response = $client->post(
                env('IDP_HOST') . '/api/v1/customers/set/password',
                [
                    'headers' => [
                        'Accept' => 'application/json',
                        'Authorization' => 'Bearer ' . $request->bearerToken(),
                    ],

                    'form_params' => $data
                ]
            );
        } catch (ClientException $e) {
            $response = $e->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $error = json_decode($responseBodyAsString);

            return $this->sendErrorResponse($error->message, [
                'message' => $error->message,
                'hint' => $response,
                'target' => 'query',
                'details' => $error
            ], 500);
        }

        return $this->sendSuccessResponse([], 'Password set Successfully');
    }

    /**
     * Verify OTP for Login
     *
     * @param $request
     * @return JsonResponse
     * @throws BLServiceException
     * @throws CurlRequestException
     */
    public function verifyOTPForLogin($request)
    {
        $number = $request->input('username');
        // validate the number
        $validate_response = $this->blCustomerService->getCustomerInfoByNumber("88" . $number);
        $response_status = $validate_response->getData()->status;

        if ($response_status != 'SUCCESS') {
            $this->logService->LoginLog("Login with OTP", "Number Not valid", $number, HttpStatusCode::BAD_REQUEST, HttpStatusCode::BAD_REQUEST,
                "Number Not valid", $request->otp, $request->otp_token);

            return $this->sendErrorResponse(
                "The number is not valid banglalink number",
                [
                    'message' => 'The number is not valid banglalink number',
                ],
                HttpStatusCode::BAD_REQUEST
            );
        }

        $customer_info = $validate_response->getData()->data;

        if (!$this->isUserExist($number)) {
            // register user
            $registerResponse = $this->register($customer_info, $number);
        }

        //login with otp perform

        $otp_grant_data = [
            'grant_type' => 'otp_grant',
            'client_id' => $request->client_id,
            'client_secret' => $request->client_secret,
            'otp' => $request->otp,
            'username' => $request->username,
            'provider' => $request->provider,
        ];

        $token = IdpIntegrationService::otpGrantTokenRequest($otp_grant_data);

        if ($token['http_code'] != 200) {
            $error_response = json_decode($token['data'], true);
            $message = $error_response['message'];
            if ($error_response['error'] == 'invalid_otp') {
                $message = 'Your OTP is invalid';
            }

            $this->logService->LoginLog("Login with OTP", "Invalid OTP", $number, $token['http_code'], HttpStatusCode::BAD_REQUEST,
                "invalid_otp", $request->otp, $request->otp_token);

            return $this->sendErrorResponse(
                $message,
                [
                    'message' => $message,
                    'hint' => 'Getting HTTP error from IDP',
                    'details' => $error_response,
                ],
                400
            );
        }
//        $customerBasic = IdpIntegrationService::getCustomerBasicInfo($number);

//        $customer_info2 = json_decode($customerBasic['data'], true);
        $user = Customer::where('phone', $number)->first();
//        $lastLoginAt = isset($user->last_login_at) ? $user->last_login_at : null;

//        $customer = $this->blCustomerService->getCustomerInfoByNumber($customer_info2['data']['msisdn']);
//        $customerResponseData = $customer->getData();


        $response = IdpIntegrationService::getCustomerInfo($number);

        if ($response['http_code'] != 200) {

            $this->logService->LoginLog("Login with OTP", "Problem in IDP", $number, $response['http_code'], HttpStatusCode::BAD_REQUEST,
                "Getting HTTP error from IDP", $request->otp, $request->otp_token);

            return $this->sendErrorResponse(
                'IDP Customer info Problem',
                [
                    'message' => 'Something went wrong. try again later',
                    'hint' => 'Getting HTTP error from IDP',
                    'details' => [],
                ],
                400
            );
        }

//        $data = json_decode($response['data'], true);

//        $this->updateNumberTypeForOTPLogin($request, $user, $customerResponseData);

        if ($user->customer_account_id != $customer_info->id) {
            $this->customerRepository->updateCustomerAccountId($customer_info->msisdn, $customer_info->id);
        }

//        $customer = $this->customerService->prepareCustomerBasicInfo($user, $data['data']);

        $this->logService->LoginLog("Login with OTP", "success", $number, $response['http_code'], HttpStatusCode::SUCCESS,
            "success", $request->otp, $request->otp_token);

//        if(Carbon::parse($lastLoginAt)->diffInDays(Carbon::now()) > 90) {
//            dispatch(new ProcessHibernateCustomerLoginBonus('LoginBonus', $user))->onQueue('process-bonus');
//        }


        return $this->sendSuccessResponse(
            [
                'token' => json_decode($token['data']),
//                'customer' => $customer,
                "is_new_user" => isset($registerResponse) && $registerResponse
            ],
            'Customer login with OTP'
        );
    }
}
