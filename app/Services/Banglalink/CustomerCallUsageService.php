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

class CustomerCallUsageService extends BaseService
{
    /**
     * @var ApiBaseService
     */
    public $responseFormatter;
    protected const CALL_USAGE_API_ENDPOINT = "/usages-history/usages/customer-usages-history/call-usages-history";
    protected const INCOMING_TRANSACTION_TYPE = "incoming_calls";
    protected const OUTGOING_TRANSACTION_TYPE = "outgoing_calls";
    /**
     * @var CustomerService
     */
    protected $customerService;

    public function __construct(ApiBaseService $apiBaseService, CustomerService $customerService)
    {
        $this->responseFormatter = $apiBaseService;
        $this->customerService = $customerService;
    }

    protected function checkValidDateFormat($format)
    {
        return (bool)strtotime($format);
    }

    public function getCallUsageUrl($customer_id, $from, $to, $transactionType)
    {
        return self::CALL_USAGE_API_ENDPOINT . "?" .
            "from=$from&to=$to&subscriptionId=$customer_id&transactionType=$transactionType";
    }

    private function formatUnit($amount)
    {
        return (int) $amount;
    }

    private function formatCost($amount)
    {
        return round($amount, 2); // given in tk.
    }

    public function getIncomingUsage($customer_id, $from, $to, $transaction_type)
    {
        $redis_key = "incoming_call:" . $customer_id . ':' . $from . '-' . $to;

        $redis_ttl = env('USAGE_HISTORY_TTL_SECONDS', 300);

//        $ttl_settings = MyBlAppSettings::where('key', MyBlAppSettingsKey::USAGE_HISTORY_TTL_SECONDS)->first();
//        if ($ttl_settings) {
//            $redis_ttl = json_decode($ttl_settings->value)->value;
//        }

        if (!$call_usage = Redis::get($redis_key)) {
            $response_data = $this->get($this->getCallUsageUrl($customer_id, $from, $to, $transaction_type));
            $data = $this->prepareIncomingUsageHistory(json_decode($response_data['response']));

            $call_usage = json_encode($data);
            Redis::setex($redis_key, $redis_ttl, $call_usage);
        }

        return json_decode($call_usage, true);
    }

    public function getOutgoingUsage($customer_id, $from, $to, $transaction_type)
    {
        $redis_key = "outgoing_call:" . $customer_id . ':' . $from . '-' . $to;

        $redis_ttl = env('USAGE_HISTORY_TTL_SECONDS', 300);

//        $ttl_settings = MyBlAppSettings::where('key', MyBlAppSettingsKey::USAGE_HISTORY_TTL_SECONDS)->first();
//        if ($ttl_settings) {
//            $redis_ttl = json_decode($ttl_settings->value)->value;
//        }

        if (!$call_usage = Redis::get($redis_key)) {
            $response_data = $this->get($this->getCallUsageUrl($customer_id, $from, $to, $transaction_type));
            $data = $this->prepareOutgoingUsageHistory(json_decode($response_data['response']));

            $call_usage = json_encode($data);
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

        $redis_key = "call_usage:" . $customer_id . ':' . $request->from . '-' . $request->to;

        $redis_ttl = env('USAGE_HISTORY_TTL_SECONDS', 300);

//        $ttl_settings = MyBlAppSettings::where('key', MyBlAppSettingsKey::USAGE_HISTORY_TTL_SECONDS)->first();
//        if ($ttl_settings) {
//            $redis_ttl = json_decode($ttl_settings->value)->value;
//        }

        if (!$call_usage = Redis::get($redis_key)) {
            $outgoing_usage = $this->getOutgoingUsage(
                $customer_id,
                $request->from,
                $request->to,
                self::OUTGOING_TRANSACTION_TYPE
            );

            $incoming_usage = $this->getIncomingUsage(
                substr($user->msisdn, 3),
                $request->from,
                $request->to,
                self::INCOMING_TRANSACTION_TYPE
            );

            $call_usage_data = array_merge($outgoing_usage, $incoming_usage);

            $formatted_data = $this->prepareCallUsageHistory($call_usage_data);

            $call_usage = json_encode($formatted_data);
            Redis::setex($redis_key, $redis_ttl, $call_usage);
        }

        return $this->responseFormatter->sendSuccessResponse(json_decode($call_usage, true), 'Call Usage History');
    }

    public function prepareCallUsageHistory($data)
    {
        $collection = collect($data)->sortByDesc('date');

        return $collection->values()->all();
    }

    public function prepareIncomingUsageHistory($data)
    {
        $incoming_data = [];
        if (!empty($data->data)) {
            foreach ($data->data as $usage_data) {
                $item = $usage_data->attributes;
                if ($item) {
                    $incoming_data [] = [
                        'date' => $this->checkValidDateFormat($item->eventAt) ?
                            Carbon::parse($item->eventAt)->setTimezone('UTC')->toDateTimeString() : null,
                        'number' => $item->callingNumber,
                        'is_outgoing' => false,
                        'duration' => $this->formatUnit($item->duration),
                        'duration_unit' => 'seconds',
                        'cost' => $this->formatCost(0),
                    ];
                }
            }
        }

        return $incoming_data;
    }

    public function prepareOutgoingUsageHistory($data)
    {
        $outgoing_data = [];
        if (!empty($data->data)) {
            foreach ($data->data as $usage_data) {
                $item = $usage_data->attributes;
                if ($item) {
                    $outgoing_data [] = [
                        'date' => $this->checkValidDateFormat($item->eventAt) ?
                            Carbon::parse($item->eventAt)->setTimezone('UTC')->toDateTimeString() : null,
                        'number' => $item->calledNumber,
                        'is_outgoing' => true,
                        'duration' => $this->formatUnit($item->duration),
                        'duration_unit' => 'seconds',
                        'cost' => $this->formatCost($item->transactionAmount)
                    ];
                }
            }
        }

        return $outgoing_data;
    }
}
