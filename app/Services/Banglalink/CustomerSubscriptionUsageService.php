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

class CustomerSubscriptionUsageService extends BaseService
{
    /**
     * @var ApiBaseService
     */
    public $responseFormatter;
    protected const SUBSCRIPTION_USAGE_API_ENDPOINT = "/usages-history/usages/customer-usages-history/subscription-usages";
    protected const INCLUDE_TYPE = "product.fees";
    protected const STATUS = "all";
    /**
     * @var CustomerService
     */
    protected $customerService;

    public function __construct(ApiBaseService $apiBaseService, CustomerService $customerService)
    {
        $this->responseFormatter = $apiBaseService;
        $this->customerService = $customerService;
    }

    public function getSubscriptionUsageUrl($customer_id, $includeType, $status)
    {
        return self::SUBSCRIPTION_USAGE_API_ENDPOINT . "?" .
            "subscriptionId=$customer_id&include=$includeType&status=$status";
    }

    protected function checkValidDateFormat($format)
    {
        return (bool) strtotime($format);
    }

    /*    private function formatCost($amount)
        {
            return round($amount, 2); // given in paisa. converted to taka
        }*/

    public function getSubscriptionUsageHistory(Request $request)
    {
        $user = $this->customerService->getCustomerDetails($request);

        if (!$user) {
            return $this->responseFormatter->sendErrorResponse("User not found", [], HttpStatusCode::UNAUTHORIZED);
        }

        $customer_id = $user->customer_account_id;

        $redis_key = "subscription_usage:" . $customer_id . ':' . $request->from . '-' . $request->to;

        $redis_ttl = env('USAGE_HISTORY_TTL_SECONDS', 300);

//        $ttl_settings = MyBlAppSettings::where('key', MyBlAppSettingsKey::USAGE_HISTORY_TTL_SECONDS)->first();
//        if ($ttl_settings) {
//            $redis_ttl = json_decode($ttl_settings->value)->value;
//        }

        if (!$subscription_usage = Redis::get($redis_key)) {
            $response_data = $this->get($this->getSubscriptionUsageUrl(
                $customer_id,
                self::INCLUDE_TYPE,
                self::STATUS
            ));

            $formatted_data = $this->prepareSubscriptionUsageHistory(json_decode($response_data['response']));

            $from = Carbon::parse($request->from)
                ->startOfDay()        // 2018-09-29 00:00:00.000000
                ->toDateTimeString(); // 2018-09-29 00:00:00

            $to = Carbon::parse($request->to)
                ->endOfDay()          // 2018-09-29 23:59:59.000000
                ->toDateTimeString(); // 2018-09-29 23:59:59

            /*        $formatted_data = collect($formatted_data)->filter(function ($item) use ($from) {
                        return $item['deactivated_date'] == null || $item['deactivated_date'] >= $from;
                    });*/

            $formatted_data = collect($formatted_data)->whereBetween('activated_date', [$from, $to]);

            $formatted_data = $formatted_data->sortByDesc('activated_date');

            $subscription_usage = json_encode($formatted_data->values());
            Redis::setex($redis_key, $redis_ttl, $subscription_usage);
        }
        return $this->responseFormatter
            ->sendSuccessResponse(json_decode($subscription_usage, true), 'Subscription Usage History');
    }

    public function prepareSubscriptionUsageHistory($data)
    {
        $subscription_data = [];
        if (!empty($data)) {
            foreach ($data as $item) {
                if ($item) {
                    $subscription_data [] = [
                        'service_name' => $item->productName,
                        'activated_date' => ($this->checkValidDateFormat($item->activatedAt)) ?
                            Carbon::parse($item->activatedAt)->setTimezone('UTC')->toDateTimeString() : null,
                        'deactivated_date' => ($item->deactivatedAt) ? Carbon::parse($item->deactivatedAt)
                            ->setTimezone('UTC')
                            ->toDateTimeString() : null,
                        'billing_date' => null,
                        'is_active' => $item->active,
                        'is_auto_renew' => false,
                        'fee' => round($item->fee, 2)
                    ];
                }
            }
        }

        return $subscription_data;
    }

    public function getSummary($customer_id, $from, $to)
    {
        $response_data = $this->get($this->getSubscriptionUsageUrl(
            $customer_id,
            self::INCLUDE_TYPE,
            self::STATUS
        ));

        $from = Carbon::parse($from)
            ->startOfDay()        // 2018-09-29 00:00:00.000000
            ->toDateTimeString(); // 2018-09-29 00:00:00

        $to = Carbon::parse($to)
            ->endOfDay()          // 2018-09-29 23:59:59.000000
            ->toDateTimeString(); // 2018-09-29 23:59:59


        $formatted_data = $this->prepareSubscriptionUsageHistory(json_decode($response_data['response']));

        $formatted_data = collect($formatted_data)->whereBetween('activated_date', [$from, $to]);
/*        $formatted_data = collect($formatted_data)->filter(function ($item) use ($from) {
            return $item['deactivated_date'] == null || $item['deactivated_date'] >= $from;
        });*/

        $cost = collect($formatted_data)->sum('fee');
        $active_count = collect($formatted_data)->count();

        return [
            'cost' => round($cost, 2),
            'active_count' => $active_count
        ];
    }
}
