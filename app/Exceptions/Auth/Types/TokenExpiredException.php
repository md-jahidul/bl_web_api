<?php

namespace App\Exceptions\Auth\Types;

use App\Exceptions\Auth\Interfaces\IExceptionResponse;
use Tymon\JWTAuth\Facades\JWTAuth;

class TokenExpiredException implements IExceptionResponse
{
    public function response()
    {
        try {

            $refreshed = JWTAuth::refresh(JWTAuth::getToken());

            return response()->json([
                'message' => 'Token expiration',
                'refreshed_token' => $refreshed
            ], 401);

        } catch (\Exception $e) {

            if( $e instanceof \Tymon\JWTAuth\Exceptions\TokenBlacklistedException ){
                return (new TokenBlackListException)->response();
            }

            return (new GenericException)->response();
        }
    }
}
