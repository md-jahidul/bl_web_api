<?php

namespace App\Services\Banglalink;

use App\Enums\HttpStatusCode;
use App\Enums\MyBlAppSettingsKey;
use App\Models\MyBlAppSettings;
use App\Repositories\CustomerRepository;
use App\Services\ApiBaseService;
use App\Services\CustomerService;
use App\Services\IdpIntegrationService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class CustomerRoamingUsageService extends BaseService
{
    /**
     * @var ApiBaseService
     */
    public $responseFormatter;
    protected const ROAMING_USAGE_API_ENDPOINT = "/usages-history/usages/customer-usages-history/roaming-usages";
    protected const SMS_TRANSACTION_TYPE = "roaming_sms";
    protected const DATA_TRANSACTION_TYPE = "roaming_data";
    protected const CALL_TRANSACTION_TYPE = "roaming_calls";
    /**
     * @var CustomerService
     */
    protected $customerService;

    public function __construct(ApiBaseService $apiBaseService, CustomerService $customerService)
    {
        $this->responseFormatter = $apiBaseService;
        $this->customerService = $customerService;
    }

    public function getRoamingUsageUrl($customer_id, $from, $to, $transactionType)
    {
        return self::ROAMING_USAGE_API_ENDPOINT . "?" .
            "from=$from&to=$to&subscriptionId=$customer_id&transactionType=$transactionType";
    }

    private function formatUnit($transaction_type, $amount)
    {
        if ($transaction_type == 'call') {
            return (int)$amount; // given in seconds. converted to min
        }

        if ($transaction_type == 'internet') {
            return round($amount / 1024 / 1024, 2);
        }

        return (int)$amount; // for SMS
    }

    private function formatCost($amount)
    {
        return (double)($amount); //in taka
    }

    public function getCallUsage($customer_id, $from, $to)
    {
        $redis_key = "roaming_call:" . $customer_id . ':' . $from . '-' . $to;

        $redis_ttl = env('USAGE_HISTORY_TTL_SECONDS', 300);

//        $ttl_settings = MyBlAppSettings::where('key', MyBlAppSettingsKey::USAGE_HISTORY_TTL_SECONDS)->first();
//        if ($ttl_settings) {
//            $redis_ttl = json_decode($ttl_settings->value)->value;
//        }

        if (!$call_usage = Redis::get($redis_key)) {
            $response_data = $this->get($this->getRoamingUsageUrl(
                $customer_id,
                $from,
                $to,
                self::CALL_TRANSACTION_TYPE
            ));

            $formatted_data = $this->prepareCallUsageHistory(json_decode($response_data['response']));

            $formatted_data = collect($formatted_data)->sortByDesc('date')->values();

            $call_usage = json_encode($formatted_data);
            Redis::setex($redis_key, $redis_ttl, $call_usage);
        }
        return json_decode($call_usage, true);
    }

    public function getCallUsageHistory(Request $request)
    {
        $user = $this->customerService->getCustomerDetails($request);

        if (!$user) {
            return $this->responseFormatter->sendErrorResponse("User not found", [], HttpStatusCode::UNAUTHORIZED);
        }

        $customer_id = $user->customer_account_id;
        $call_usage_data = $this->getCallUsage(
            $customer_id,
            $request->from,
            $request->to
        );

        return $this->responseFormatter->sendSuccessResponse($call_usage_data, 'Roaming Call Usage History');
    }

    public function prepareCallUsageHistory($data)
    {
        $call_data = [];
        if (!empty($data->data)) {
            foreach ($data->data as $usage_data) {
                $item = $usage_data->attributes;
                if ($item) {
                    $call_data [] = [
                        'date' => Carbon::parse($item->eventAt)->toDateTimeString(),
                        'number' => $item->calledNumber,
                        'is_outgoing' => ($item->transactionType == 'Outgoing call') ? true : false,
                        'duration' => $this->formatUnit('call', $item->duration),
                        'cost' => $this->formatCost($item->transactionAmount)
                    ];
                }
            }
        }

        return $call_data;
    }


    public function getDataUsageHistory(Request $request)
    {
        $user = $this->customerService->getCustomerDetails($request);

        if (!$user) {
            return $this->responseFormatter->sendErrorResponse("User not found", [], HttpStatusCode::UNAUTHORIZED);
        }

        $customer_id = $user->customer_account_id;
        $call_usage_data = $this->getDataUsage(
            $customer_id,
            $request->from,
            $request->to
        );

        return $this->responseFormatter->sendSuccessResponse($call_usage_data, 'Roaming Data Usage History');
    }

    public function getDataUsage($customer_id, $from, $to)
    {
        $redis_key = "roaming_data:" . $customer_id . ':' . $from . '-' . $to;

        $redis_ttl = env('USAGE_HISTORY_TTL_SECONDS', 300);

//        $ttl_settings = MyBlAppSettings::where('key', MyBlAppSettingsKey::USAGE_HISTORY_TTL_SECONDS)->first();
//        if ($ttl_settings) {
//            $redis_ttl = json_decode($ttl_settings->value)->value;
//        }

        if (!$roaming_data = Redis::get($redis_key)) {
            $response_data = $this->get($this->getRoamingUsageUrl(
                $customer_id,
                $from,
                $to,
                self::DATA_TRANSACTION_TYPE
            ));

            $formatted_data = $this->prepareDataUsageHistory(json_decode($response_data['response']));

            $formatted_data = collect($formatted_data)->sortByDesc('start_time')->values();

            $roaming_data = json_encode($formatted_data);
            Redis::setex($redis_key, $redis_ttl, $roaming_data);
        }

        return json_decode($roaming_data, true);
    }

    public function prepareDataUsageHistory($data)
    {
        $internet_data = [];
        if (!empty($data->data)) {
            foreach ($data->data as $usage_data) {
                $item = $usage_data->attributes;
                if ($item) {
                    $internet_data [] = [
                        'start_time' => Carbon::parse($item->eventAt)->toDateTimeString(),
                        'end_time' => Carbon::parse($item->eventAt)->addSeconds($item->duration)
                            ->toDateTimeString(),
                        'usage' => $this->formatUnit('internet', $item->dataAmount),
                        'cost' => $this->formatCost($item->transactionAmount)
                    ];
                }
            }
        }

        return $internet_data;
    }

    public function getSmsUsageHistory(Request $request)
    {
        $user = $this->customerService->getCustomerDetails($request);

        if (!$user) {
            return $this->responseFormatter->sendErrorResponse("User not found", [], HttpStatusCode::UNAUTHORIZED);
        }

        $customer_id = $user->customer_account_id;
        $call_usage_data = $this->getSmsUsage(
            $customer_id,
            $request->from,
            $request->to
        );

        return $this->responseFormatter->sendSuccessResponse($call_usage_data, 'Roaming SMS Usage History');
    }

    public function getSmsUsage($customer_id, $from, $to)
    {
        $redis_key = "roaming_sms:" . $customer_id . ':' . $from . '-' . $to;

        $redis_ttl = env('USAGE_HISTORY_TTL_SECONDS', 300);

//        $ttl_settings = MyBlAppSettings::where('key', MyBlAppSettingsKey::USAGE_HISTORY_TTL_SECONDS)->first();
//        if ($ttl_settings) {
//            $redis_ttl = json_decode($ttl_settings->value)->value;
//        }

        if (!$roaming_sms = Redis::get($redis_key)) {
            $response_data = $this->get(
                $this->getRoamingUsageUrl($customer_id, $from, $to, self::SMS_TRANSACTION_TYPE)
            );

            $formatted_data = $this->prepareSmsUsageHistory(json_decode($response_data['response']));

            $formatted_data = collect($formatted_data)->sortByDesc('date')->values();

            $roaming_sms = json_encode($formatted_data);
            Redis::setex($redis_key, $redis_ttl, $roaming_sms);
        }
        return json_decode($roaming_sms, true);
    }

    public function prepareSmsUsageHistory($data)
    {
        $sms_data = [];
        if (!empty($data->data)) {
            foreach ($data->data as $usage_data) {
                $item = $usage_data->attributes;
                if ($item) {
                    $sms_data [] = [
                        'date' => Carbon::parse($item->eventAt)->toDateTimeString(),
                        'number' => $item->calledNumber,
                        'is_outgoing' => true,
                        'usage' => $this->formatUnit('sms', $item->duration),
                        'cost' => $this->formatCost($item->transactionAmount)
                    ];
                }
            }
        }

        return $sms_data;
    }

    public function prepareSummaryUsageData($customer_id, $from, $to)
    {
        $call_usage_data = collect($this->getCallUsage($customer_id, $from, $to))->sum('cost');

        $sms_usage_data = collect($this->getSmsUsage($customer_id, $from, $to))->sum('cost');

        $data_usage_data = collect($this->getDataUsage($customer_id, $from, $to))->sum('cost');

        return [
            'internet' => $data_usage_data,
            'minutes' => $call_usage_data,
            'recharge' => 0,
            'sms' => $sms_usage_data
        ];
    }

    public function getSummaryUsageHistory(Request $request)
    {
        $user = $this->customerService->getCustomerDetails($request);

        if (!$user) {
            return $this->responseFormatter->sendErrorResponse("User not found", [], HttpStatusCode::UNAUTHORIZED);
        }

        $customer_id = $user->customer_account_id;

        $redis_key = "roaming_usage_summary:" . $customer_id . ':' . $request->from . '-' . $request->to;

        $redis_ttl = env('USAGE_HISTORY_TTL_SECONDS', 300);

//        $ttl_settings = MyBlAppSettings::where('key', MyBlAppSettingsKey::USAGE_HISTORY_TTL_SECONDS)->first();
//        if ($ttl_settings) {
//            $redis_ttl = json_decode($ttl_settings->value)->value;
//        }

        if (!$roaming_usage_summary = Redis::get($redis_key)) {
            $data = $this->prepareSummaryUsageData($customer_id, $request->from, $request->to);
            $roaming_usage_summary = json_encode($data);
            Redis::setex($redis_key, $redis_ttl, $roaming_usage_summary);
        }

        return $this->responseFormatter->sendSuccessResponse(
            json_decode($roaming_usage_summary, true),
            'Roaming Usage Summary'
        );
    }
}
