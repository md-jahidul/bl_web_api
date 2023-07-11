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
        $this->setActionRepository($blLabsUserRepository);
    }

    public function register($request)
    {
        //Validate data
        $data = $request->only('name', 'email', 'password');
        $validator = Validator::make($data, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|max:50'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        //Request is valid, create new user
        $user = BlLabUser::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        //User created, return success response
        return $this->sendSuccessResponse($user, 'User register successfully');
    }

    public function sendOTP($request)
    {
        try {
            $request->validate([
                'email' => 'required|email|max:255|unique:bl_lab_users',
            ]);

            $otp = rand(1000,9999);
            $data = [
                'to' => $request->email,
                'subject' => "Email Verification",
                'body' => 'Your OTP is : '. $otp
            ];
            Redis::setex($request->email, 300, $otp);
            dispatch(new SendEmailJob($data));

            return $this->sendSuccessResponse([], 'OTP sent successfully');
        } catch (QueryException $exception) {
            return $this->sendErrorResponse('OTP send failed', $exception->getMessage(), '500',);
        }
    }
    public function verifyOTP($request)
    {
//        $validator = Validator::make($request->all(), [
//            'email' => 'required|email|max:255',
//            'password' => 'required|max:6|min:6',
//        ]);
//
//        $token = auth('api')->attempt($validator->validated());
//        dd($token);


        $otp = Redis::get($request->email);
        if (!$otp) {
            return $this->sendErrorResponse('OTP verification failed', 'OTP session time is expired', 404);
        }

        if ($otp == $request->otp) {
            if ($token = auth()->attempt())
            return $this->sendSuccessResponse([], 'OTP verify successfully');
        } else {
            return $this->sendErrorResponse('OTP verification failed', 'Invalid OTP', 401);
        }
    }

    protected function responseWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expire_in' => '' /*auth()->factory()*/
        ]);
    }
}
