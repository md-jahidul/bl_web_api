<?php

namespace App\Http\Middleware;

use App\Exceptions\RequestUnauthorizedException;
use Closure;

class VerifyFacebookUpsellKey {

    public function handle($request, Closure $next)
    {

        $upsellKey = $request->header('authorization');

        if (! $upsellKey == config('facebookupsell.api_key')) {
            throw new RequestUnauthorizedException();
        }

        return $next($request);
    }

}