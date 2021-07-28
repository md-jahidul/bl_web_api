<?php

namespace App\Services;

use App\Exceptions\RequestUnauthorizedException;
use App\Exceptions\SecreteTokenExpireException;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class SecreteTokenService extends ApiBaseService
{

    /**
     * @return array
     * @throws Exception
     */
    public function encryptToken(): array
    {
        $token = bin2hex(random_bytes(32));
        $strLn = str_split($token, strlen($token)/2);
        $partOneRev = array_reverse(str_split($strLn[0]));
        $partOne = implode($partOneRev);
        $partTwoRev = array_reverse(str_split($strLn[1]));
        $partTwo = implode($partTwoRev);
        $arrayReverse = $partTwo.$partOne;
        $convBase64 = str_replace('=', '', base64_encode($arrayReverse));

        return [
            'token' => $token,
            'secret_key' => $convBase64
        ];
    }

    /**
     * @return JsonResponse|mixed
     * @throws Exception
     */
    public function generateToken()
    {
        $encryptedToken = $this->encryptToken();
        $millisecondsKey = (int) str_replace('.', '', microtime(true) * 1000);
        $secretCode = $millisecondsKey.uniqid();

        $cashData = [
            'secret_key' => $encryptedToken['secret_key']
        ];

        // Set Token in Redis Cache
        Redis::setex("al_api_security_key:$secretCode", 120, json_encode($cashData));
        $data['secret_code'] = $millisecondsKey;

        $data = [
            '_token' => $encryptedToken['token'],
            'secret_code' => $secretCode
        ];
        return $this->sendSuccessResponse($data, 'Token successfully generated');
    }

}
