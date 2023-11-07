<?php

namespace App\Services\Banglalink;

use App\Enums\HttpStatusCode;
//use App\Enums\MyBlAppSettingsKey;
//use App\Models\MyBlAppSettings;
use App\Services\ApiBaseService;
use App\Services\Banglalink\CustomerRechargeHistoryService;
use App\Services\Banglalink\CustomerRoamingUsageService;
use App\Services\Banglalink\CustomerSubscriptionUsageService;
use App\Services\Banglalink\PriyojonService;
use App\Services\CustomerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class CustomerSummaryUsageService extends BaseService
{
    protected $customerService;
    /**
     * @var ApiBaseService
     */
    protected $responseFormatter;
    /**
     * @var CustomerCallUsageService
     */
    protected $callUsageService;
    /**
     * @var CustomerSmsUsageService
     */
    protected $smsUsageService;
    /**
     * @var CustomerInternetUsageService
     */
    protected $internetUsageService;
    /**
     * @var CustomerRoamingUsageService
     */
    protected $roamingUsageService;
    /**
     * @var CustomerRechargeHistoryService
     */
    protected $rechargeHistoryService;
    /**
     * @var CustomerSubscriptionUsageService
     */
    protected $subscriptionUsageService;
    /**
     * @var PriyojonService
     */
    private $priyojonService;

    /**
     * CustomerSummaryUsageService constructor.
     * @param ApiBaseService $apiBaseService
     * @param CustomerService $customerService
     * @param CustomerCallUsageService $callUsageService
     * @param CustomerSmsUsageService $smsUsageService
     * @param CustomerInternetUsageService $internetUsageService
     * @param CustomerRoamingUsageService $roamingUsageService
     * @param CustomerRechargeHistoryService $rechargeHistoryService
     * @param CustomerSubscriptionUsageService $subscriptionUsageService
     * @param PriyojonService $priyojonService
     */
    public function __construct(
        ApiBaseService $apiBaseService,
        CustomerService $customerService,
        CustomerCallUsageService $callUsageService,
        CustomerSmsUsageService $smsUsageService,
        CustomerInternetUsageService $internetUsageService,
        CustomerRoamingUsageService $roamingUsageService,
        CustomerRechargeHistoryService $rechargeHistoryService,
        CustomerSubscriptionUsageService $subscriptionUsageService,
        PriyojonService $priyojonService
    ) {
        $this->responseFormatter = $apiBaseService;
        $this->customerService = $customerService;
        $this->callUsageService = $callUsageService;
        $this->smsUsageService = $smsUsageService;
        $this->internetUsageService = $internetUsageService;
        $this->roamingUsageService = $roamingUsageService;
        $this->rechargeHistoryService = $rechargeHistoryService;
        $this->subscriptionUsageService = $subscriptionUsageService;
        $this->priyojonService = $priyojonService;
    }

    public function prepareSummaryUsageHistory($customer_id, $from, $to, $subscription_id_incoming)
    {
        $redis_key = "summary_usage:" . $customer_id . ':' . $from . '-' . $to;
        $redis_ttl = env('USAGE_HISTORY_TTL_SECONDS', 300);

//        $ttl_settings = MyBlAppSettings::where('key', MyBlAppSettingsKey::USAGE_HISTORY_TTL_SECONDS)->first();
//        if ($ttl_settings) {
//            $redis_ttl = json_decode($ttl_settings->value)->value;
//        }

        if (!$summary_usage = Redis::get($redis_key)) {
            $outgoing_call_usage_data = $this->callUsageService->getOutgoingUsage(
                $customer_id,
                $from,
                $to,
                'outgoing_calls'
            );
            $outgoing_call_usage_cost = collect($outgoing_call_usage_data)->sum('cost');
            $outgoing_total_call_usage = collect($outgoing_call_usage_data)->sum('duration');

            $incoming_call_usage_data = $this->callUsageService->getIncomingUsage(
                $subscription_id_incoming,
                $from,
                $to,
                'incoming_calls'
            );

            $incoming_call_usage_cost = collect($incoming_call_usage_data)->sum('cost');
            $incoming_total_call_usage = collect($incoming_call_usage_data)->sum('duration');

            $sms_usage_data = $this->smsUsageService->getOutgoingSmsUsageData($customer_id, $from, $to, 'sms');
            $sms_usage_cost = collect($sms_usage_data)->sum('cost');
            $total_sms_usage = collect($sms_usage_data)->sum('usage');

            $incoming_sms_usage_data = $this->smsUsageService->getIncomingSmsUsageData(
                $subscription_id_incoming,
                $from,
                $to,
                'incoming_sms'
            );
            $incoming_sms_usage_cost = collect($incoming_sms_usage_data)->sum('cost');
            $incoming_total_sms_usage = collect($incoming_sms_usage_data)->sum('usage');

            $internet_usage_data = $this->internetUsageService->getInternetUsageData(
                $customer_id,
                $from,
                $to,
                'data_usage'
            );

            $internet_usage_cost = collect($internet_usage_data)->sum('cost');
            $total_internet_usage = collect($internet_usage_data)->sum('usage');

            $roaming_usage_data = $this->roamingUsageService->prepareSummaryUsageData($customer_id, $from, $to);
            $roaming_usage_cost = collect($roaming_usage_data)->sum();

            $recharge_usage_data = $this->rechargeHistoryService->prepareRechargeHistoryData(
                $customer_id,
                $from,
                $to,
                'recharge'
            );

            $recharge_usage_cost = collect($recharge_usage_data)->sum('amount');

            $subscription_usage_data = $this->subscriptionUsageService->getSummary($customer_id, $from, $to);
            $totalMin = ($outgoing_total_call_usage + $incoming_total_call_usage) / 60;
            $minutes = [
                'title' => 'Minutes',
                'total' => floor($totalMin),
                'unit' => 'Min',
                'cost' => round(($outgoing_call_usage_cost + $incoming_call_usage_cost), 2),
                'message' => 'Your minute usage in total'
            ];

            $internet = [
                'title' => 'Internet',
                'total' => $total_internet_usage,
                'unit' => 'mb',
                'cost' => round($internet_usage_cost, 2),
                'message' => 'Your data usage in total'
            ];

            $sms = [
                'title' => 'SMS',
                'total' => $total_sms_usage + $incoming_total_sms_usage,
                'unit' => 'SMS',
                'cost' => round(($sms_usage_cost + $incoming_sms_usage_cost), 2),
                'message' => 'Your SMS usage in total'
            ];

            $roaming = [
                'title' => 'Roaming',
                'total' => $roaming_usage_cost,
                'unit' => 'BDT',
                'cost' => round($roaming_usage_cost, 2),
                'message' => 'Your roaming usage in total'
            ];

            $recharge = [
                'title' => 'Recharge',
                'total' => $recharge_usage_cost,
                'unit' => 'TK',
                'cost' => round($recharge_usage_cost, 2),
                'message' => 'Your recharge amount in total'
            ];

            $vas = [
                'title' => 'Subscriptions',
                'total' => $subscription_usage_data['active_count'],
                'unit' => '',
                'cost' => round($subscription_usage_data['cost'], 2),
                'message' => 'Your Subscription price in total'
            ];

            /* $orangePointsResult = $this->priyojonService->priyojonUsageHistoryTotal($subscription_id_incoming, $from,
                $to, 'orange_points');
            if (!$orangePointsResult) {
                $totalClubPoint = 0;
            } else {
                $totalClubPoint = $orangePointsResult['orange_points'] ?? 0;
            } */

            // $orange_points = [
            //     'title' => 'Orange Club points',
            //     'total' => $totalClubPoint,
            //     'total' => 0,
            //     'unit' => '',
            //     'cost' => 0,
            //     'message' => 'Orange points'
            // ];

            $summary = [
                'total' => round($minutes['cost'] /*+ $internet['cost']*/ +
                    $sms['cost'] + $roaming['cost'] + $vas ['cost'], 2),
                'minutes' => $minutes,
                'sms' => $sms,
                'roaming' => $roaming,
                'recharge' => $recharge,
                'vas' => $vas,
                'internet' => $internet,
                'orange_points' => null
            ];

            Redis::setex($redis_key, $redis_ttl, json_encode($summary));

            return $summary;
        }


        return json_decode($summary_usage, true);
    }

    public function prepareTotalUsageAmount($customer_id, $from, $to)
    {
        $redis_key = "total_usage_amount:" . $customer_id . ':' . $from . '-' . $to;

        $redis_ttl = env('USAGE_HISTORY_TTL_SECONDS', 300);

//        $ttl_settings = MyBlAppSettings::where('key', MyBlAppSettingsKey::USAGE_HISTORY_TTL_SECONDS)->first();
//        if ($ttl_settings) {
//            $redis_ttl = json_decode($ttl_settings->value)->value;
//        }

        if (!$total_usage_cost = Redis::get($redis_key)) {
            $outgoing_call_usage_data = $this->callUsageService->getOutgoingUsage(
                $customer_id,
                $from,
                $to,
                'outgoing_calls'
            );
            $outgoing_call_usage_cost = collect($outgoing_call_usage_data)->sum('cost');

            $sms_usage_data = $this->smsUsageService->getOutgoingSmsUsageData($customer_id, $from, $to, 'sms');
            $sms_usage_cost = collect($sms_usage_data)->sum('cost');

            /*        $internet_usage_data = $this->internetUsageService->getInternetUsageData(
                        $customer_id,
                        $from,
                        $to,
                        'data_usage'
                    );

                    $internet_usage_cost = collect($internet_usage_data)->sum('cost');*/

            $roaming_usage_data = $this->roamingUsageService->prepareSummaryUsageData($customer_id, $from, $to);
            $roaming_usage_cost = $roaming_usage_data['minutes'] + $roaming_usage_data['sms'];

            $subscription_usage_data = $this->subscriptionUsageService->getSummary($customer_id, $from, $to);

            $total_usage_cost = $outgoing_call_usage_cost
                + $sms_usage_cost + $roaming_usage_cost + $subscription_usage_data['cost'];

            Redis::setex($redis_key, $redis_ttl, $total_usage_cost);
        }
        return [
            'total' => round($total_usage_cost, 2)
        ];
    }

    public function getSummaryUsageHistory(Request $request)
    {
        $user = $this->customerService->getCustomerDetails($request);

        if (!$user) {
            return $this->responseFormatter->sendErrorResponse("User not found", [], HttpStatusCode::UNAUTHORIZED);
        }

        $customer_id = $user->customer_account_id;

//        if ($request->content_for == 'home') {
//            $data = $this->prepareTotalUsageAmount($customer_id, $request->from, $request->to);
//            return $this->responseFormatter->sendSuccessResponse($data, 'Usage Summary Total Amount');
//        }

        $data = $this->prepareSummaryUsageHistory($customer_id, $request->from, $request->to, substr($user->msisdn, 3));

        return $this->responseFormatter->sendSuccessResponse($data, 'Usage Summary Data');
    }
}
