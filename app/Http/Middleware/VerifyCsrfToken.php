<?php

namespace App\Http\Middleware;

use App\Models\AlCsrfToken;
use Carbon\Carbon;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Closure;
use Illuminate\Session\TokenMismatchException;

class VerifyCsrfToken extends Middleware
{
    /**
     * Indicates whether the XSRF-TOKEN cookie should be set on the response.
     *
     * @var bool
     */
    protected $addHttpCookie = true;

    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '/api/v1/success',
        '/api/v1/cancel',
        '/api/v1/failure'
    ];


    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     *
     * @throws \Illuminate\Session\TokenMismatchException
     */
    public function handle($request, Closure $next)
    {
        if (
//            $this->runningUnitTests() ||
//            $this->inExceptArray($request) ||
            $this->isReading($request) ||
            $this->tokenExpireCheck($request) &&
        $this->tokensMatch($request)
        ) {
            return tap($next($request), function ($response) use ($request) {
                if ($this->shouldAddXsrfTokenCookie()) {
                    $this->addCookieToResponse($request, $response);
                }
            });
        }
        throw new TokenMismatchException('CSRF token mismatch.');
    }

    /**
     * Determine if the session and input CSRF tokens match.
     *
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    protected function tokensMatch($request)
    {
        $token = $this->getTokenFromRequest($request);
        $dbToken = AlCsrfToken::where('token', $token)->first();

        if ($dbToken) {
            return is_string($dbToken->token) &&
                is_string($token) &&
                hash_equals($dbToken->token, $token);
        }
        return false;
    }

    protected function tokenExpireCheck($request)
    {
        $bdTimeZone = Carbon::now('Asia/Dhaka');
        $dateTime = $bdTimeZone->toDateTimeString();

        $token = $this->getTokenFromRequest($request);
        $existToken = AlCsrfToken::where('token', $token)
            ->where(function ($query) use ($dateTime) {
                $query->where('expires_at', '>=', $dateTime)
                    ->orWhereNull('expires_at');
            })->first();

        if ($existToken) {
            return true;
        }
        return false;
    }
}
