<?php

namespace App\Services\BlLabs;

use App\Jobs\SendEmailJob;
use App\Mail\BlLabUserOtpSend;
use App\Models\BlLabUser;
use App\Repositories\BlLabsUserRepository;
use App\Services\ApiBaseService;
use App\Traits\CrudTrait;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;


class BlLabsUserService extends ApiBaseService
{
    use CrudTrait;

    /**
     * @var BlLabsUserRepository
     */
    private $blLabsUserRepository;

    /**
     * BlLabsUserService constructor.
     * @param BlLabsUserRepository $blLabsUserRepository
     */
    public function __construct(
        BlLabsUserRepository $blLabsUserRepository
    ) {
        $this->blLabsUserRepository = $blLabsUserRepository;
//        $this->middleware('auth:api', ['except' => ['login']]);
        $this->setActionRepository($blLabsUserRepository);
    }

    public function register($request)
    {

        //Validate data
        $request = $request->only('name', 'email', 'password', 'secret_token');

        // Verify authorized request
        $redisSecretToken = Redis::get("secret_token_" . $request['email']);

        if (!$redisSecretToken) {
            return $this->sendErrorResponse('Token expired', "Token session is expired", '404',);
        }

        if ($redisSecretToken != $request['secret_token']){
            return $this->sendErrorResponse('Unauthorized', "Secret token is invalid", '401',);
        }

        //Request is valid, create new user
        $user = BlLabUser::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => bcrypt($request['password'])
        ]);

        $token = auth('api')->attempt(['email' => $user->email, 'password' => $user->password]);
        $data = $this->responseWithToken($token);
        //User created, return success response
        $data['user'] = [
            'email' => $user->email,
            'avatar' => null
        ];
        return $this->sendSuccessResponse($data, 'User register successfully');
    }

    public function sendOTP($request)
    {
        try {
            $request->validate([
                'email' => 'required|email|max:255|unique:bl_lab_users',
            ]);

            $otp = rand(100000,999999);
            $data = [
                'to' => $request->email,
                'subject' => "Email Verification",
                'body' => 'Your OTP is : '. $otp
            ];
            $ttl = 300;
            Redis::setex($request->email, $ttl, $otp);
            dispatch(new SendEmailJob($data));

            return $this->sendSuccessResponse(['otp_expire_in' => $ttl], 'OTP sent successfully');
        } catch (QueryException $exception) {
            return $this->sendErrorResponse('OTP send failed', $exception->getMessage(), '500',);
        }
    }
    public function verifyOTP($request)
    {
//        $token = auth('api')->attempt($validator->validated());

        $otp = Redis::get($request->email);
        if (!$otp) {
            return $this->sendErrorResponse('OTP verification failed', 'OTP session time is expired', 401);
        }

        if ($otp == $request->otp) {
            $secretToken = bin2hex(random_bytes(30));
//            dd($secretToken);
            Redis::setex("secret_token_" . $request->email, 1800, $secretToken);
            return $this->sendSuccessResponse(['secret_token' => $secretToken], 'OTP verify successfully');
        } else {
            return $this->sendErrorResponse('OTP verification failed',
                'Oops! The OTP you provided is incorrect. Please check your email and provide the correct OTP.', 401);
        }
    }

    protected function responseWithToken($token)
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expire_in' => $this->guard()->factory()->getTTL() * 60
        ];
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public function guard()
    {
        return Auth::guard();
    }
}
