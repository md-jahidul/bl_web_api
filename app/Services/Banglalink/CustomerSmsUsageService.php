<?php

namespace App\Services\Banglalink;

use App\Enums\HttpStatusCode;
use App\Enums\MyBlAppSettingsKey;
use App\Exceptions\BLServiceException;
use App\Exceptions\CurlRequestException;
use App\Models\MyBlAppSettings;
use App\Services\ApiBaseService;
use App\Services\CustomerService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class CustomerSmsUsageService extends BaseService
{
    /**
     * @var ApiBaseService
     */
    public $responseFormatter;
    protected const SMS_USAGE_API_ENDPOINT = "/usages-history/usages/customer-usages-history/sms-usages-history";
    protected const TRANSACTION_TYPE = "sms";
    /**
     * @var CustomerService
     */
    protected $customerService;

    public function __construct(ApiBaseService $apiBaseService, CustomerService $customerService)
    {
        $this->responseFormatter = $apiBaseService;
        $this->customerService = $customerService;
    }

    public function getSmsUsageUrl($customer_id, $from, $to, $transactionType)
    {
        return self::SMS_USAGE_API_ENDPOINT . "?" .
            "from=$from&to=$to&subscriptionId=$customer_id&transactionType=$transactionType";
    }

    private function formatUnit($amount)
    {
        return (int)$amount; // given in SMS
    }

    private function formatCost($amount)
    {
        return round($amount, 2); // given in paisa. converted to taka
    }

    protected function checkValidDateFormat($format)
    {
        return (bool)strtotime($format);
    }

    /**
     * @param $customer_id
     * @param $from
     * @param $to
     * @param $transaction_type
     * @return array
     * @throws BLServiceException
     * @throws CurlRequestException
     */
    public function getOutgoingSmsUsageData($customer_id, $from, $to, $transaction_type)
    {
        $redis_key = "outgoing_sms:" . $customer_id . ':' . $from . '-' . $to;

        $redis_ttl = env('USAGE_HISTORY_TTL_SECONDS', 300);

//        $ttl_settings = MyBlAppSettings::where('key', MyBlAppSettingsKey::USAGE_HISTORY_TTL_SECONDS)->first();
//        if ($ttl_settings) {
//            $redis_ttl = json_decode($ttl_settings->value)->value;
//        }

        if (!$sms_usage = Redis::get($redis_key)) {
            $response_data = $this->get($this->getSmsUsageUrl($customer_id, $from, $to, $transaction_type));
            $data = $this->prepareOutgoingSmsUsageHistory(json_decode($response_data['response']));

            $sms_usage = json_encode($data);
            Redis::setex($redis_key, $redis_ttl, $sms_usage);
        }

        return json_decode($sms_usage, true);
    }

    public function getIncomingSmsUsageData($subscription_id, $from, $to, $transaction_type)
    {
        $redis_key = "incoming_sms:" . $subscription_id . ':' . $from . '-' . $to;

        $redis_ttl = env('USAGE_HISTORY_TTL_SECONDS', 300);

//        $ttl_settings = MyBlAppSettings::where('key', MyBlAppSettingsKey::USAGE_HISTORY_TTL_SECONDS)->first();
//        if ($ttl_settings) {
//            $redis_ttl = json_decode($ttl_settings->value)->value;
//        }

        if (!$sms_usage = Redis::get($redis_key)) {
            $response_data = $this->get($this->getSmsUsageUrl($subscription_id, $from, $to, $transaction_type));
            $data = $this->prepareIncomingSmsUsageHistory(json_decode($response_data['response']));

            $sms_usage = json_encode($data);
            Redis::setex($redis_key, $redis_ttl, $sms_usage);
        }

        return json_decode($sms_usage, true);
    }

    public function getSmsUsageHistory(Request $request)
    {
        $user = $this->customerService->getCustomerDetails($request);

        if (!$user) {
            return $this->responseFormatter->sendErrorResponse("User not found", [], HttpStatusCode::UNAUTHORIZED);
        }

        $customer_id = $user->customer_account_id;

        $redis_key = "sms_usage:" . $customer_id . ':' . $request->from . '-' . $request->to;

        $redis_ttl = env('USAGE_HISTORY_TTL_SECONDS', 300);

//        $ttl_settings = MyBlAppSettings::where('key', MyBlAppSettingsKey::USAGE_HISTORY_TTL_SECONDS)->first();
//        if ($ttl_settings) {
//            $redis_ttl = json_decode($ttl_settings->value)->value;
//        }

        if (!$sms_usage = Redis::get($redis_key)) {
            $outgoing = $this->getOutgoingSmsUsageData(
                $customer_id,
                $request->from,
                $request->to,
                self::TRANSACTION_TYPE
            );

            $incoming = $this->getIncomingSmsUsageData(
                substr($user->msisdn, 3),
                $request->from,
                $request->to,
                'incoming_sms'
            );

            $sms_usage_data = array_merge($outgoing, $incoming);
            $formatted_data = $this->prepareSMSUsageHistory($sms_usage_data);

            $sms_usage = json_encode($formatted_data);
            Redis::setex($redis_key, $redis_ttl, $sms_usage);
        }

        return $this->responseFormatter->sendSuccessResponse(json_decode($sms_usage, true), 'SMS Usage History');
    }

    public function prepareSMSUsageHistory($data)
    {
        $collection = collect($data)->sortByDesc('date');

        return $collection->values()->all();
    }

    public function prepareOutgoingSmsUsageHistory($data)
    {
        $sms_data = [];
        if (!empty($data->data)) {
            foreach ($data->data as $usage_data) {
                $item = $usage_data->attributes;
                if ($item) {
                    $sms_data [] = [
                        'date' => ($this->checkValidDateFormat($item->eventAt)) ?
                            Carbon::parse($item->eventAt)->toDateTimeString() : null,
                        'number' => $item->calledNumber,
                        'is_outgoing' => true,
                        'usage' => $this->formatUnit($item->duration),
                        'cost' => $this->formatCost($item->transactionAmount)
                    ];
                }
            }
        }

        return $sms_data;
    }

    public function prepareIncomingSmsUsageHistory($data)
    {
        $sms_data = [];
        if (!empty($data->data)) {
            foreach ($data->data as $usage_data) {
                $item = $usage_data->attributes;
                if ($item) {
                    $sms_data [] = [
                        'date' => ($this->checkValidDateFormat($item->eventAt)) ?
                            Carbon::parse($item->eventAt)->toDateTimeString() : null,
                        'number' => $item->callingNumber,
                        'is_outgoing' => false,
                        'usage' => $this->formatUnit($item->duration),
                        'cost' => $this->formatCost($item->transactionAmount)
                    ];
                }
            }
        }

        return $sms_data;
    }
}
