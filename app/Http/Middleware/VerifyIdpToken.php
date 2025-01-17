<?php

namespace App\Http\Middleware;

use App\Exceptions\RequestUnauthorizedException;
use App\Services\IdpIntegrationService;
use Closure;

class VerifyIdpToken {

    public function handle($request, Closure $next)
    {

        $idpToken = $request->header('authorization');
        $response = IdpIntegrationService::tokenValidationRequest(['token' => $idpToken]);
        $responseData = json_decode($response['data'], true);

        if (!isset($responseData['token_status']) || $responseData['token_status'] === 'Invalid') {
            throw new RequestUnauthorizedException();
        }

        return $next($request);
    }

}