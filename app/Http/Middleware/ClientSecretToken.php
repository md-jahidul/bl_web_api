<?php

namespace App\Http\Middleware;

use App\Exceptions\RequestUnauthorizedException;
use App\Exceptions\SecreteTokenExpireException;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class ClientSecretToken
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     * @throws RequestUnauthorizedException
     * @throws SecreteTokenExpireException
     */
    public function handle($request, Closure $next)
    {
        // return $next($request);
        if ($this->validateToken($request)){
            return $next($request);
        }
        $errorRes = $this->logFormat($request);
        Log::channel("clientSecretToken")->error($errorRes);
        throw new RequestUnauthorizedException();
    }

    /**
     * @throws SecreteTokenExpireException
     */
    public function validateToken($request)
    {
        try {
            $clientSecurityToken = $request->header('client-security-token');
            $clientSecurityTokenArr = explode('=', $clientSecurityToken);

            $redisKey = "al_api_security_key:" . $clientSecurityTokenArr[0];
            $secretToken = $clientSecurityTokenArr[1];

            $cacheData = Redis::get($redisKey);
            $cacheData = json_decode($cacheData, true);

            if (!$cacheData) {
                throw new SecreteTokenExpireException();
            }

            if (hash_equals($cacheData['secret_key'], $secretToken)) {
                Redis::del($redisKey);
                return true;
            }
        } catch (\Exception $exception){
            Log::error("Token Validate Failed:" . $exception->getMessage());
        }
    }

    protected function logFormat($request): array
    {
        $clientSecurityToken = $request->header('client-security-token');
        $clientIp = request()->ip();
        $endpoint = request()->getRequestUri();
        return [
            'client_ip' => $clientIp,
            'api_end_point' => $endpoint,
            'error_message' => 'UNAUTHORIZED request. Invalid token: ' . $clientSecurityToken
        ];
    }
}
