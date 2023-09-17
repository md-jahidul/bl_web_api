<?php

namespace App\Http\Middleware;

use App\Exceptions\Auth\AuthExceptionService;
use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthJwt extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            JWTAuth::parseToken()->authenticate();
        }
        catch (\Exception $e) {
            $jwtException = (new AuthExceptionService($e))->getType();
            return $jwtException->response();
        }

        return $next($request);
    }
}
