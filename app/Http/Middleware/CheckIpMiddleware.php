<?php

namespace App\Http\Middleware;

use Closure;

class CheckIpMiddleware
{
    public $whiteIps = ['172.16.229.242', '127.0.0.1'];
//    public $whiteIps = ['172.16.229.242'];
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!in_array($request->ip(), $this->whiteIps)) {
            return response()->json(['Request from invalid IP address']);
        }

        return $next($request);
    }
}
