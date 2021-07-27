<?php

namespace App\Services;

use App\Exceptions\RequestUnauthorizedException;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class SecreteTokenService extends ApiBaseService
{
    /**
     * @return JsonResponse|mixed
     * @throws Exception
     */
    public function generateToken()
    {
//        $token_expiry = config('session.lifetime');
//        $bdTimeZone = Carbon::now('Asia/Dhaka');
//        $currentTime = $bdTimeZone->toDateTimeString();


        $token = bin2hex(random_bytes(32));
        $strLn = str_split($token, strlen($token)/2);
        $partOneRev = array_reverse(str_split($strLn[0]));
        $partOne = implode($partOneRev);
        $partTwoRev = array_reverse(str_split($strLn[1]));
        $partTwo = implode($partTwoRev);
        $arrayReverse = $partTwo.$partOne;

        $convBase64 = str_replace('=', '', base64_encode($arrayReverse));

        $millisecondsKey = (int) str_replace('.', '', microtime(true) * 1000);

        $cashData = [
            '_token' => $token,
            'secret_key' => $convBase64
        ];

        // Set Token in Redis Cache
        Redis::setex("al_api_security_key:$millisecondsKey", 500, json_encode($cashData));
        $data['secret_code'] = $millisecondsKey;

        $data = [
            '_token' => $token,
            'secret_key' => $convBase64,
            'secret_code' => $millisecondsKey
        ];
        return $this->sendSuccessResponse($data, 'Token successfully generated');
    }

    /**
     * @throws RequestUnauthorizedException
     */
    public function validateToken(Request $request)
    {
       $token = $request->header('client-security-token');
       $secret_key = $request->header('server-security-token');
       $redis_key = $request->header('secret-code');

       $cacheData = Redis::get("al_api_security_key:$redis_key");
       $cacheData = json_decode($cacheData, true);
        if ($cacheData) {
            if (hash_equals($cacheData['_token'], $token) &&
                hash_equals($cacheData['secret_key'], $secret_key)) {
                return true;
            }
        }

        throw new RequestUnauthorizedException();
    }
}
