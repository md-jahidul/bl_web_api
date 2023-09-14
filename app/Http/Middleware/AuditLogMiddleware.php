<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use App\Services\CustomerService;
use Closure;
use Illuminate\Support\Facades\Log;

class AuditLogMiddleware
{
    /**
     * @var CustomerService
     */
    private $customerService;

    public function __construct(CustomerService $customerService)
    {
        return $this->customerService = $customerService;
    }

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

            if ($request->header('authorization')) {
                $customerInfo = $this->customerService->getCustomerDetails($request);
                $number = $customerInfo->msisdn;
            } else {
                $number = $request->header('msisdn');
            }

            if (preg_match('/^[0-9]*$/', $number)) {
                $msisdn = $number;
            } else {
                $msisdn = "";
            }

            $this->saveAuditLogs($request, $msisdn);

        } catch (\Exception $e) {
            Log::error('Audit Log Error : ' . $e->getMessage());
        }

        return $next($request);
    }


    /**
     * @param $request
     * @param $msisdn
     */
    private function saveAuditLogs($request, $msisdn)
    {
        AuditLog::create([
            'msisdn' => $msisdn,
            'source' => 'assetlite',
            'browse_url' => $request->path(),
            'browser_info' => $request->header('browser'),
            'user_ip' => $request->ip()
        ]);
    }
}
