<?php

namespace App\Services\BlLabs;

use App\Enums\HttpStatusCode;
use App\Jobs\SendEmailJob;
use App\Mail\BlLabUserOtpSend;
use App\Models\BlLab\BlLabUser;
use App\Repositories\BlLab\BlLabsAuthenticationRepository;
use App\Services\ApiBaseService;
use App\Traits\CrudTrait;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;

class BlLabsAuthenticationService extends ApiBaseService
{
    use CrudTrait;

    /**
     * @var BlLabsAuthenticationRepository
     */
    private $blLabsUserRepository;

    /**
     * BlLabsAuthenticationService constructor.
     * @param BlLabsAuthenticationRepository $blLabsUserRepository
     */
    public function __construct(
        BlLabsAuthenticationRepository $blLabsUserRepository
    ) {
        $this->blLabsUserRepository = $blLabsUserRepository;
        $this->setActionRepository($blLabsUserRepository);
    }

    public function login($data)
    {
        $credentials = request(['email', 'password']);

        $user = $this->blLabsUserRepository->findOneByProperties(['email' => $data['email']], ['email']);

        if (!$user) {
            return $this->sendErrorResponse("Not registered",'The email is not registered.', HttpStatusCode::NOT_FOUND);
        }

        if (! $token = auth()->attempt($credentials)) {
            return $this->sendErrorResponse('Unauthorized', "Invalid credential", HttpStatusCode::UNAUTHORIZED);
        }

        $response = $this->responseWithToken($token);
        $response['user'] = [
            'email' => $user->email,
            'avatar' => null
        ];

        return $this->sendSuccessResponse($response, 'Successful Attempt');
    }

    public function register($request)
    {
        //Validate data
        $request = $request->only('email', 'password', 'secret_token');

        // Verify authorized request
        $redisSecretToken = Redis::get("secret_token_" . $request['email']);

        if (!$redisSecretToken) {
            return $this->sendErrorResponse('Token expired', "Token session is expired", HttpStatusCode::NOT_FOUND,);
        }

        if ($redisSecretToken != $request['secret_token']){
            return $this->sendErrorResponse('Unauthorized', "Secret token is invalid", HttpStatusCode::INVALID_TOKEN);
        }

        $credentials = request(['email', 'password']);
        //Request is valid, create new user
        $user = BlLabUser::create($credentials);

        if (! $token = auth()->attempt($credentials)) {
            return $this->sendErrorResponse('Unauthorized', "Incorrect Email or Password", HttpStatusCode::UNAUTHORIZED);
        }

        $data = $this->responseWithToken($token);

        //User created, return success response
        $data['user'] = [
            'email' => $user->email,
            'avatar' => null
        ];

        $secretTokenKey = "secret_token_" . $request['email'];
        Redis::del($secretTokenKey);
        return $this->sendSuccessResponse($data, 'User register successfully');
    }

    public function sendOTP($request)
    {
        try {

            if (!$request->is_reg_request) {
                $user = $this->blLabsUserRepository->findOneByProperties(['email' => $request->email], ['email']);
                if (!$user) {
                    return $this->sendErrorResponse("Not Registered",'The email is not registered', HttpStatusCode::NOT_FOUND);
                }
            }

            if ($request->is_reg_request) {
                $user = $this->blLabsUserRepository->findOneByProperties(['email' => $request->email], ['email']);
                if ($user) {
                    return $this->sendErrorResponse("Already Registered",'This email is already registered. Try Login instead.', HttpStatusCode::ALREADY_EXIST);
                }
            }

            $otp = rand(100000,999999);
            $ttl = 60 * 5; // 5 min
            $data = [
                'to' => $request->email,
                'subject' => "Your One-Time Password (OTP)",
                'otp' => $otp,
                'otp_expire_in' => $ttl / 60
            ];
            Redis::setex($request->email, $ttl, $otp);
            Mail::to($data['to'])->send(new BlLabUserOtpSend($data));
            // dispatch(new SendEmailJob($data));

            return $this->sendSuccessResponse(['otp_expire_in' => $ttl], 'OTP sent successfully');
        } catch (QueryException $exception) {
            return $this->sendErrorResponse('OTP send failed', $exception->getMessage(), '500',);
        }
    }
    public function verifyOTP($request)
    {
        $otp = Redis::get($request->email);
        if (!$otp) {
            return $this->sendErrorResponse('OTP verification failed', 'OTP session time is expired', 401);
        }

        if ($otp == $request->otp) {
            $secretToken = bin2hex(random_bytes(30));
            Redis::setex("secret_token_" . $request->email, 1800, $secretToken);
            return $this->sendSuccessResponse(['secret_token' => $secretToken], 'OTP verify successfully');
        } else {
            return $this->sendErrorResponse('OTP verification failed',
                'Oops! The OTP you provided is incorrect. Please check your email and provide the correct OTP.', 401);
        }
    }

    public function refreshToken()
    {
        $data = $this->responseWithToken(auth()->refresh());
        return $this->sendSuccessResponse($data, 'Refresh token generate successfully!');
    }

    public function forgetPassword($request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => [
                'required',
                'min:8',
                'confirmed'
            ],
            'secret_token' => 'required'
        ]);

        $request = $request->only('email', 'password', 'secret_token');

        // Verify authorized request
        $redisSecretToken = Redis::get("secret_token_" . $request['email']);

        if (!$redisSecretToken) {
            return $this->sendErrorResponse('Token expired', "Token session is expired", '404',);
        }

        if ($redisSecretToken != $request['secret_token']){
            return $this->sendErrorResponse('Unauthorized', "Secret token is invalid", '401',);
        }

        $blLabUser =  BlLabUser::where('email', $request['email'])->first();

        if (!$blLabUser) {
            return $this->sendErrorResponse('Unauthorized', "Email address not found", '401',);
        }

        $blLabUser->update($request);

        $secretTokenKey = "secret_token_" . $request['email'];
        Redis::del($secretTokenKey);
        return $this->sendSuccessResponse([], 'Password new set successfully!');
    }

    public function prepareSendOtp($request)
    {
        $request->validate([
            'email' => 'required|email|max:255|unique:bl_lab_users',
        ]);

        $otp = rand(100000,999999);
        $data = [
            'to' => $request->email,
            'subject' => "Email Verification",
            'body' => 'Your OTP is : '. $otp
        ];
        $ttl = 60 * 5; // 5 min
        Redis::setex($request->email, $ttl, $otp);
        dispatch(new SendEmailJob($data));
        return $ttl;
    }

    protected function responseWithToken($token)
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => (int) auth()->factory()->getTTL() * 60 // Sec
        ];
    }
}
