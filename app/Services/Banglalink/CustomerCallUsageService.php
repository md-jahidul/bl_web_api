<?php

namespace App\Services\Banglalink;

use App\Enums\HttpStatusCode;
use App\Repositories\CustomerRepository;
use App\Services\ApiBaseService;
use App\Services\CustomerService;
use App\Services\IdpIntegrationService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

    private function getCallUsageUrl($customer_id, $from, $to, $transactionType)
    {
        return self::CALL_USAGE_API_ENDPOINT . "?" .
            "from=$from&to=$to&subscriptionId=$customer_id&transactionType=$transactionType";
    }

    private function getIncomingUsage($customer_id, $from, $to, $transaction_type)
    {

        $response_data = $this->get($this->getCallUsageUrl($customer_id, $from, $to, $transaction_type));

        $formatted_incoming_usage_data = $this->prepareIncomingUsageHistory(json_decode($response_data['response']));

        return $formatted_incoming_usage_data;
    }

    private function getOutgoingUsage($customer_id, $from, $to, $transaction_type)
    {

        $response_data = $this->get($this->getCallUsageUrl($customer_id, $from, $to, $transaction_type));

        $formatted_outgoing_usage_data = $this->prepareOutgoingUsageHistory(json_decode(
            $response_data['response']
        ));

        return $formatted_outgoing_usage_data;
    }

    public function getCallUsageHistory(Request $request)
    {
        $user = $this->customerService->getAuthenticateCustomer($request);

        if (!$user) {
            return $this->responseFormatter->sendErrorResponse("User not found", [], HttpStatusCode::UNAUTHORIZED);
        }

        $customer_id = $user->customer_account_id;
        $outgoing_usage = $this->getOutgoingUsage(
            $customer_id,
            $request->from,
            $request->to,
            self::OUTGOING_TRANSACTION_TYPE
        );

        $incoming_usage = $this->getIncomingUsage(
            $customer_id,
            $request->from,
            $request->to,
            self::INCOMING_TRANSACTION_TYPE
        );

        $call_usage_data = array_merge($outgoing_usage, $incoming_usage);
        $formatted_data = $this->prepareCallUsageHistory($call_usage_data);

        return $this->responseFormatter->sendSuccessResponse($formatted_data, 'Call Usage History');
    }

    private function prepareCallUsageHistory($data)
    {
        $collection = collect($data)->sortBy(function ($obj) {
            return $obj['date'];
        });

        return $collection;
    }

    private function prepareIncomingUsageHistory($data)
    {
        $incoming_data = [];
        if (!empty($data->data)) {
            foreach ($data->data as $usage_data) {
                $item = $usage_data->attributes;
                if ($item) {
                    $incoming_data [] = [
                        'date' => Carbon::parse($item->eventAt)->setTimezone('UTC')->toDateTimeString(),
                        'number' => $item->calledNumber,
                        'is_outgoing' => false,
                        'duration' => $item->duration,
                        'cost' => 0,
                    ];
                }
            }
        }

        return $incoming_data;
    }

    private function prepareOutgoingUsageHistory($data)
    {
        $outgoing_data = [];
        if (!empty($data->data)) {
            foreach ($data->data as $usage_data) {
                $item = $usage_data->attributes;
                if ($item) {
                    $outgoing_data [] = [
                        'date' => Carbon::parse($item->eventAt)->setTimezone('UTC')->toDateTimeString(),
                        'number' => $item->calledNumber,
                        'is_outgoing' => true,
                        'duration' => $item->duration,
                        'cost' => (int)$item->transactionAmount,
                    ];
                }
            }
        }

        return $outgoing_data;
    }
}
