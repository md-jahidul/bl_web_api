<?php

namespace App\Http\Middleware;

use App\Exceptions\RequestUnauthorizedException;
use Closure;

class VerifyFacebookUpsellKey {

    public function handle($request, Closure $next)
    {
        $upsellKey = $request->header('api-key');
        $timestamp = $request->header('timestamp');
        
        if (!isset($upsellKey) || !isset($timestamp) ) {
            throw new RequestUnauthorizedException();
        }

        $blUpsellSecret = config('facebookupsell.bl_upsell_secret');
        $hash = hash_hmac('sha256', $timestamp, $blUpsellSecret);
        $hashBase64Encoded = base64_encode($hash);

        if (! hash_equals($hashBase64Encoded, $upsellKey)) {
            throw new RequestUnauthorizedException();
        }

        return $next($request);
    }

}