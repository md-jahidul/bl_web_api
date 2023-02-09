<?php
namespace App\Services;

use App\Models\BlOtpLoginLogs;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Class LogService
 * @package App\Services
 */
class LogService
{

    /**
     * @param $msg
     * @param $data
     * @param null $number
     */
    public function OtpLog($data, $msg=null, $number=null)
    {
        $payload = [
            'message' => $msg,
            'data' => json_encode($data),
            'msisdn' => $number,
            'status' => $data['status_code'],
            'date'   => Carbon::now()->toDateString(),
        ];

        try{
            Log::channel('blOtpLog')->info($payload);
        } catch(Exception $exception){

        }

        $this->logToDatabase($payload);

    }

    /**
     * @param $data
     * @param $msg
     * @param null $number
     * @param null $status
     * @param null $status_code_header
     * @param null $response_message
     * @param null $otp
     * @param null $otp_token
     */
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
        } catch(Exception $exception){}


        $this->logToDatabase($payload);
    }

    /**
     * @param $msg
     * @param $data
     * @param null $number
     */
    public function BonusLog($data, $msg=null, $number=null)
    {
        $payload = [
            'message' => $msg,
            'data' => $data,
            'msisdn' => $number
        ];

        try{
            Log::channel('blBonusLog')->info($payload);
        } catch(Exception $exception){

        }
    }

    /**
     * @param $payload
     */
    protected function logToDatabase($payload): void
    {
        try {
            BlOtpLoginLogs::create($payload);
        } catch (Exception $exception) {
            Log::error("login log entry error");
        }
    }

    /**
     * Checks if the OTP request is valid to send to the requested number
     * @param $number
     * @param int $statusHeader
     * @return array
     */
    public function validateOtpRule($number, $statusHeader = 202)
    {
        $valid = false;
        $message = "";

        $today = Carbon::now();
        $otpToday = BlOtpLoginLogs::where('msisdn', $number)
            ->where('status_code_header', $statusHeader)
            ->where('date', $today->toDateString());

        if ($otpToday->count() < 20) {
            $checkTime = $today->addSeconds(-30)->toDateTimeString();
            $checkRecent = $otpToday->where('created_at', '>=', $checkTime) ->orderBy('id', 'desc')->count();
            if (!$checkRecent) {
                $valid = true;
            } else {
                $message = "Current OTP session is still running. Please try after sometime";
            }
        } else {
            $message = "Maximum number of OTP request exceeded for today. Please try again tomorrow";
        }

        return ['valid' => $valid, 'message' => $message];
    }

}
