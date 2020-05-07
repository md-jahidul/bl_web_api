<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;

class AuditLogMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        try {
            $msisdn = "01999998963";
            $this->saveAuditLogs($request, $msisdn);

        } catch (\Exception $e) {
            Log::error('Audit Log Error : ' . $e->getMessage());
        }

        return $next($request);
    }


    /**
     * @param $request
     * @param $user
     */
    private function saveAuditLogs($request, $msisdn)
    {
        AuditLog::create([
            'msisdn' => $request->header('msisdn'),
            'source' => 'assetlite',
            //'source' => $request->header('platform'),
            'browse_url' => $request->path(),
            'browser_info' => $request->header('browser'),
            'user_ip' => $request->ip(),
            'device_id' => "",
        ]);
    }
}
