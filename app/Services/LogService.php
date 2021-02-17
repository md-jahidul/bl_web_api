<?php

namespace App\Services;


use App\Models\AlOtpLoginLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class LogService extends ApiBaseService
{

    public function LoginLog($data, $msg=null, $number=null, $status=null, $status_code_header=null, $response_message=null, $otp=null, $otp_token=null)
    {
        $payload = [
            'message' => $msg,
            'data' => json_encode($data),
            'msisdn' => $number,
            'status' => $status,
            'date'   => Carbon::now()->toDateString(),
            'status_code_header' => $status_code_header,
            'response_message' => $response_message,
            'otp' => $otp,
            'otp_token' => $otp_token,
        ];

        try{
            Log::channel('blLoginLog')->info($payload);
        } catch(\Exception $exception){}


        $this->logToDatabase($payload);
    }

    protected function logToDatabase($payload): void
    {
        try {
            AlOtpLoginLog::create($payload);
        } catch (\Exception $exception) {
            Log::error("login log entry error");
        }
    }

    public function getRecentOtpRequest($number, $status = 200)
    {
        $checkTime = Carbon::now()->addMinutes(-4)->toDateTimeString();
        return AlOtpLoginLog::where('msisdn', $number)
            ->where('status', $status)
            ->where('created_at', '>=', $checkTime)
            ->orderBy('id', 'desc')
            ->first();
    }
}
