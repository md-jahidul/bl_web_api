<?php

namespace App\Services\Banglalink;

use App\Enums\HttpStatusCode;
use App\Enums\MyBlAppSettingsKey;
use App\Models\Customer;
use App\Models\MyBlAppSettings;
use App\Repositories\CustomerRepository;
use App\Services\ApiBaseService;
use App\Services\CustomerService;
use App\Services\IdpIntegrationService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;


/**
 * Class CustomerRechargeHistoryService
 * @package App\Services\Banglalink
 */
class CustomerRechargeHistoryService extends BaseService
{
    /**
     * @var ApiBaseService
     */
    public $responseFormatter;
    protected const RECHARGE_USAGE_API_ENDPOINT = "/usages-history/recharge/recharge-history/getRechargeHistory";
    protected const TRANSACTION_TYPE = "recharge";
    protected const POSTPAID_RECHARGE_PAYMENT_API_ENDPOINT="/usages-history/payment/payment-history/getPaymentHistory";

    /**
     * @var CustomerService
     */
    protected $customerService;


    /**
     * CustomerRechargeHistoryService constructor.
     * @param ApiBaseService $apiBaseService
     * @param CustomerService $customerService
     */
    public function __construct(ApiBaseService $apiBaseService, CustomerService $customerService)
    {
        $this->responseFormatter = $apiBaseService;
        $this->customerService = $customerService;
    }


    /**
     * @param $customer_id
     * @param $from
     * @param $to
     * @param $transactionType
     * @return string
     */
    public function getRechargeHistoryUrl($customer_id, $from, $to, $transactionType)
    {
        return self::RECHARGE_USAGE_API_ENDPOINT . "?" .
            "from=$from&to=$to&subscriptionId=$customer_id&transactionType=$transactionType";
    }


    /**
     * @param $customer_id
     * @param $from
     * @param $to
     * @param $transaction_type
     * @return array|\Illuminate\Support\Collection
     * @throws \App\Exceptions\BLServiceException
     * @throws \App\Exceptions\CurlRequestException
     */
    public function prepareRechargeHistoryData($customer_id, $from, $to, $transaction_type)
    {
        $response_data = $this->get($this->getRechargeHistoryUrl($customer_id, $from, $to, $transaction_type));

        $formatted_data = $this->prepareRechargeHistory(json_decode($response_data['response']));

        $formatted_data = collect($formatted_data)->sortByDesc('date')->values();
        return $formatted_data;
    }


    /**
     * @param $customer_id
     * @param $from
     * @param $to
     * @return array|\Illuminate\Support\Collection
     * @throws \App\Exceptions\BLServiceException
     * @throws \App\Exceptions\CurlRequestException
     */
    private function preparePostpaidPaymentHistoryData($customer_id, $from, $to)
    {
        $url = self::POSTPAID_RECHARGE_PAYMENT_API_ENDPOINT."?customerId=".$customer_id;

        $response_data = $this->get($url);

        $formatted_data = $this->preparePaymentHistory(json_decode($response_data['response']));

       // $formatted_data = collect($formatted_data)->sortByDesc('date')->values();

        return $formatted_data;
    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \App\Exceptions\BLServiceException
     * @throws \App\Exceptions\CurlRequestException
     * @throws \App\Exceptions\TokenInvalidException
     * @throws \App\Exceptions\TokenNotFoundException
     * @throws \App\Exceptions\TooManyRequestException
     */
    public function getRechargeHistory(Request $request)
    {
        $user = $this->customerService->getCustomerDetails($request);

        if (!$user) {
            return $this->responseFormatter->sendErrorResponse("User not found", [], HttpStatusCode::UNAUTHORIZED);
        }

        $customer_id = $user->customer_account_id;

        if ($user->number_type == 'prepaid') {
            return $this->getPrepaidRechargeHistory($customer_id, $request);
        }

        if ($user->number_type == 'postpaid') {
            return $this->getPostpaidRechargeHistory($customer_id, $request);
        }
    }


    /**
     * @param $customer_id
     * @param $request
     * @return JsonResponse
     * @throws \App\Exceptions\BLServiceException
     * @throws \App\Exceptions\CurlRequestException
     */
    private function getPrepaidRechargeHistory($customer_id, $request)
    {
        $redis_key = "recharge_usage:" . $customer_id . ':' . $request->from . '-' . $request->to;

        $redis_ttl = env('USAGE_HISTORY_TTL_SECONDS', 300);

//        $ttl_settings = MyBlAppSettings::where('key', MyBlAppSettingsKey::USAGE_HISTORY_TTL_SECONDS)->first();
//        if ($ttl_settings) {
//            $redis_ttl = json_decode($ttl_settings->value)->value;
//        }

        if (!$recharge_usage = Redis::get($redis_key)) {
            $formatted_data = $this->prepareRechargeHistoryData(
                $customer_id,
                $request->from,
                $request->to,
                self::TRANSACTION_TYPE
            );

            $recharge_usage = json_encode($formatted_data);
            Redis::setex($redis_key, $redis_ttl, $recharge_usage);
        }

        return $this->responseFormatter->sendSuccessResponse(json_decode($recharge_usage, true), 'Recharge History');
    }



    /**
     * @param $customer_id
     * @param $request
     * @return JsonResponse
     * @throws \App\Exceptions\BLServiceException
     * @throws \App\Exceptions\CurlRequestException
     */
    private function getPostpaidRechargeHistory($customer_id, $request)
    {

        $redis_key = "recharge_usage:" . $customer_id . ':' . $request->from . '-' . $request->to;

        $redis_ttl = env('USAGE_HISTORY_TTL_SECONDS', 300);

//        $ttl_settings = MyBlAppSettings::where('key', MyBlAppSettingsKey::USAGE_HISTORY_TTL_SECONDS)->first();
//        if ($ttl_settings) {
//            $redis_ttl = json_decode($ttl_settings->value)->value;
//        }

        if (!$recharge_usage = Redis::get($redis_key)) {
            $formatted_data = $this->preparePostpaidPaymentHistoryData(
                $customer_id,
                $request->from,
                $request->to
            );

            $recharge_usage = json_encode($formatted_data);
            Redis::setex($redis_key, $redis_ttl, $recharge_usage);
        }

        return $this->responseFormatter->sendSuccessResponse(json_decode($recharge_usage, true), 'Payment History');

    }


    /**
     * @param $data
     * @return array
     */
    public function prepareRechargeHistory($data)
    {
        $recharge_data = [];
        if (!empty($data)) {
            foreach ($data as $item) {
                if ($item) {
                    $recharge_data [] = [
                        'date'          => Carbon::parse($item->eventAt)->toDateTimeString(),
                        'recharge_from' => $item->msisdn,
                        'amount'        => $item->transactionAmount,
                    ];
                }
            }
        }

        return $recharge_data;
    }


    /**
     * @param $data
     * @return array
     */
    private function preparePaymentHistory($data)
    {
        $recharge_data = [];
        if (!empty($data)) {
            foreach ($data as $item) {
                if ($item) {
                    $recharge_data [] = [
                        'date'          => Carbon::parse($item->receivedAt)->toDateTimeString(),
                        'recharge_from' => null,
                        'amount'        => $item->paymentAmount,
                    ];
                }
            }
        }

        return $recharge_data;
    }


}
