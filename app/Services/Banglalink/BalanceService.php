<?php

namespace App\Services\Banglalink;

use App\Enums\HttpStatusCode;
use App\Models\AlCoreProduct;
use App\Models\Customer;
use App\Repositories\CustomerRepository;
use App\Services\ApiBaseService;
use App\Services\CustomerService;
use App\Services\IdpIntegrationService;
use App\Services\NumberValidationService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BalanceService extends BaseService
{
    /**
     * @var ApiBaseService
     */
    public $responseFormatter;
    /**
     * [$numberValidationService for customer info]
     */
    public $numberValidationService;

    protected const BALANCE_API_ENDPOINT = "/customer-information/customer-information";
    protected const MINIMUM_BALANCE_FOR_LOAN = 20;
    /**
     * @var CustomerRepository
     */
    protected $customerRepository;

    /**
     * @var CustomerService
     */
    protected $customerService;
    /**
     * @var CustomerAvailableProductsService
     */
    private $availableProductsService;
    /**
     * @var SubscriptionProductService
     */
    private $subscriptionProductService;
    /**
     * @var ProductLoanService
     */
    private $loanService;

    /**
     * BalanceService constructor.
     * @param CustomerService $customerService
     * @param NumberValidationService $numberValidationService
     */
    public function __construct(
        CustomerService $customerService,
        NumberValidationService $numberValidationService,
        CustomerAvailableProductsService $availableProductsService,
        SubscriptionProductService $subscriptionProductService,
        ProductLoanService $productLoanService
    ) {
        $this->responseFormatter = new ApiBaseService();
        $this->customerRepository = new CustomerRepository();
        $this->customerService = $customerService;
        $this->numberValidationService = $numberValidationService;
        $this->availableProductsService = $availableProductsService;
        $this->subscriptionProductService = $subscriptionProductService;
        $this->loanService = $productLoanService;
    }

    /**
     * @param $customerId
     * @return string
     */
    private function getSubscriptionUrl($customerId): string
    {
        return self::BALANCE_API_ENDPOINT . '/' . $customerId . '/subscription-products';
    }

    private function getBalanceUrl($customer_id)
    {
        return self::BALANCE_API_ENDPOINT . '/' . $customer_id . '/prepaid-balances' . '?sortType=SERVICE_TYPE';
    }

    private function getBalanceUrlPostpaid($customer_id)
    {
        return self::BALANCE_API_ENDPOINT . '/' . $customer_id . '/postpaid-info';
    }

    private function getAuthenticateUser($request)
    {
        $bearerToken = ['token' => $request->header('authorization')];


        $response = IdpIntegrationService::tokenValidationRequest($bearerToken);

        $data = json_decode($response['data'], true);

        if ($response['http_code'] != 200) {
            return $this->responseFormatter->sendErrorResponse("Token is Invalid", [], HttpStatusCode::UNAUTHORIZED);
        }

        $user = $this->customerRepository->getCustomerInfoByPhone($data['user']['mobile']);

        return $user;
    }

    private function isEligibleToLoan($balance)
    {
        return random_int(0, 1) && $balance < self::MINIMUM_BALANCE_FOR_LOAN ? true : false;
    }

    /**
     * @param $response
     * @param $customer_id
     * @return mixed
     */
    private function prepareBalanceSummary($response, $customer_id)
    {
        $balance_data = collect($response->money);

        $main_balance = $balance_data->first(function ($item) {
            return $item->type == 'MAIN';
        });

        $is_eligible_to_loan =  $this->isEligibleToLoan($customer_id);
        $data['balance'] = [
            'amount' => isset($main_balance->amount) ? $main_balance->amount : 0 ,
            'unit' => isset($main_balance->unit) ? $main_balance->unit : 'Tk.',
            'expires_in' => isset($main_balance->expiryDateTime) ?
                Carbon::parse($main_balance->expiryDateTime)->setTimezone('UTC')->toDateTimeString() : null,
            'loan' => [
                'is_eligible' => $is_eligible_to_loan,
                'amount'      => ($is_eligible_to_loan) ? 30 : 0
            ]
        ];


        $talk_time = collect($response->voice);

        if ($talk_time) {
            $total_remaining_talk_time = $talk_time->sum('amount');
            $total_talk_time = $talk_time->sum('totalAmount');
            $data['minutes'] = [
                'total' => $total_talk_time,
                'remaining' => $total_remaining_talk_time,
                'unit' => 'MIN'
            ];
        }

        $sms = collect($response->sms);

        if ($sms) {
            $total_remaining_sms = $sms->sum('amount');
            $total_sms = $sms->sum('totalAmount');
            $data['sms'] = [
                'total' => $total_sms,
                'remaining' => $total_remaining_sms,
                'unit' => 'SMS'
            ];
        }


        $internet = collect($response->data);

        if ($internet) {
            $total_remaining_internet = $internet->sum('amount');
            $total_internet = $internet->sum('totalAmount');
            $data['internet'] = [
                'total' => $total_internet,
                'remaining' => $total_remaining_internet,
                'unit' => 'MB'
            ];
        }
        return $data;
    }

    /**
     * Get Balance Summary
     *
     * @param $mobile
     * @return array|JsonResponse
     */
    public function getBalanceSummary($mobile)
    {

        $validationResponse = $this->numberValidationService->validateNumberWithResponse($mobile);
        if ($validationResponse->getData()->status == 'FAIL') {
            return $validationResponse;
        }

        $customerInfo = $validationResponse->getData()->data;
        $customer_id = $customerInfo->package->customerId;

        # Postpaid balance summery
        if( $customerInfo->connectionType == 'POSTPAID' ){

            $response = $this->get($this->getBalanceUrlPostpaid($customer_id));
            $response = json_decode($response['response']);
            if (isset($response->error)) {
                return ['status' => 'FAIL', 'data' => $response->message, 'status_code' => $response->status];
            }

           // $balanceSummary = $this->prepareBalanceSummaryPostpaid($response, $customer_id);
           // $balanceSummary = $this->preparePostpaidSummary($response);
            $balanceSummary = $this->preparePostpaidBalanceSummary($response);

        }
        # Prepaid balance summery
        else{
            $response = $this->get($this->getBalanceUrl($customer_id));
            $response = json_decode($response['response']);
            if (isset($response->error)) {
                return ['status' => 'FAIL', 'data' => $response->message, 'status_code' => $response->status];
            }
            $balanceSummary = $this->prepareBalanceSummary($response, $customer_id);
        }

        $balanceSummary['connection_type'] = isset($customerInfo->connectionType) ? $customerInfo->connectionType : null;

        return ['status' => 'SUCCESS', 'data' => $balanceSummary];
    }

    /**
     * @param $response
     * @return JsonResponse|mixed
     */
    private function getBalance($response, $type)
    {
        $internet_data = collect($response->{$type});

        $data = [];
        foreach ($internet_data as $item) {
            $data [] = [
                'package_name' => isset($item->product->name) ? $item->product->name : null,
                'total' => $item->totalAmount,
                'remaining' => $item->amount,
                'unit' => $item->unit,
                'expires_in' => Carbon::parse($item->expiryDateTime)->setTimezone('UTC')->toDateTimeString(),
                'auto_renew' => false
            ];
        }
        return $data;
    }

    /**
     * @param $response
     * @return JsonResponse|mixed
     */
//    private function getMainBalance($response)
//    {
//        $balance_data = collect($response->money);
//
//        $main_balance = $balance_data->first(function ($item) {
//            return $item->type == 'MAIN';
//        });
//
//        return [
//            'remaining_balance' => [
//                'amount' => isset($main_balance->amount) ? $main_balance->amount : 0,
//                'currency' => 'Tk.',
//                'expires_in' => isset($main_balance->expiryDateTime) ?
//                    Carbon::parse($main_balance->expiryDateTime)->setTimezone('UTC')->toDateTimeString() : null
//            ],
//            'roaming_balance' => [
//                'amount' => 0,
//                'currency' => 'USD',
//                'expires_in' => null
//            ]
//        ];
//    }

    /**
     * @param $response
     * @param $customer
     * @return array
     */
    private function getMainBalance($response, $customer, $timerProducts = null)
    {
        $balance_data = collect($response->money);

        $main_balance = $balance_data->first(function ($item) {
            return $item->type == 'MAIN';
        });

        $roaming_balance_info = $balance_data->first(function ($item) {
            if (isset($item->account)) {
                return $item->account->id == "115";
            }
        });

        $customer_id = $customer->customer_account_id;

        $subscription_products = $this->subscriptionProductService->getSubscriptionProducts($customer_id);

        $rate_cutter_offer = collect($subscription_products)->first(function ($item) {
            return substr($item['code'], -3) == 'SEC';
        });

        $rate_cutter_info = null;

        if ($rate_cutter_offer) {
            $product = AlCoreProduct::where('product_code', $rate_cutter_offer ['code'])->first();
            if ($product) {
                $rate_cutter_info = [
                    'title' => $rate_cutter_offer ['commercialName'],
                    'code' => $rate_cutter_offer ['code'],
                    'fee' => $rate_cutter_offer ['fee'],
                    'rate_cutter_rate' => $product->call_rate,
                    'rate_cutter_unit_en' => $product->call_rate_unit,
                    'rate_cutter_unit_bn' => $product->product->call_rate_unit_bn ?? null,
                    'expires_in' => isset($rate_cutter_offer['deactivatedDateTime']) ?
                        Carbon::parse($rate_cutter_offer['deactivatedDateTime'])->setTimezone('UTC')->toDateTimeString() : null
                ];
            }
        }


        if (isset($roaming_balance_info->id)) {
            $roaming_balance = [
                'amount' => $roaming_balance_info->amount,
                'currency' => $roaming_balance_info->unit,
                'expires_in' => isset($roaming_balance_info->expiryDateTime) ?
                    Carbon::parse($roaming_balance_info->expiryDateTime)->setTimezone('UTC')->toDateTimeString() : null
            ];
        } else {
            $roaming_balance = [
                'amount' => 0,
                'currency' => isset($roaming_balance_info->unit) ? $roaming_balance_info->unit : "TK",
                'expires_in' => null
            ];

        }

        $data = [
            'connection_type' => 'PREPAID',
            'remaining_balance' => [
                'amount' => $main_balance->amount ?? 0,
                'currency' => 'Tk.',
                'expires_in' => isset($main_balance->expiryDateTime) ?
                    Carbon::parse($main_balance->expiryDateTime)->setTimezone('UTC')->toDateTimeString() : null
            ],
            'roaming_balance' => $roaming_balance,
            'rate_cutter' => $rate_cutter_info
        ];

        /**
         * Return customer loan information if available in balance details
         * */
//        $eligibility_cap = MyBlAppSettings::where('key', MyBlAppSettingsKey::LOAN_ELIGIBILITY_MIN_AMOUNT)->first();
//        // initially set minimum balance for all user
//        $min_amount = 10;
//        if ($eligibility_cap) {
//            $min_amount = json_decode($eligibility_cap->value)->value;
//        }

        $customer_loan_info = $this->loanService->getLoanStatus($customer_id, 'PREPAID');
        $customer_due_loan_amount = 0;

        if($customer_loan_info) {
            $customer_due_loan_amount = $customer_loan_info['due_loan'];
        }

        if($customer_due_loan_amount){
            $data['remaining_balance']['loan'] = [
                'due_loan' => $customer_due_loan_amount,
                'message' => 'Next recharge your loan amount will be deducted'
            ];
        }
        return $data;
    }


    /**
     * @param $type
     * @param Request $request
     * @return JsonResponse|mixed
     */
//    public function getBalanceDetails($type, Request $request)
//    {
//        $user = $this->getAuthenticateUser($request);
//
//        if (!$user) {
//            return $this->responseFormatter->sendErrorResponse("User not found", [], HttpStatusCode::UNAUTHORIZED);
//        }
//
//        $customer_id = ($user->customer_account_id) ? $user->customer_account_id : 8494;
//        $response = $this->get($this->getBalanceUrl($customer_id));
//        $response = json_decode($response['response']);
//
//        if (isset($response->error)) {
//            return $this->responseFormatter->sendErrorResponse(
//                $response->message,
//                [],
//                $response->status
//            );
//        }
//
//        if ($type == 'internet') {
//            return $this->getInternetBalance($response);
//        } elseif ($type == 'sms') {
//            return $this->getSmsBalance($response);
//        } elseif ($type == 'minutes') {
//            return $this->getTalkTimeBalance($response);
//        } elseif ($type == 'balance') {
//            return $this->getMainBalance($response);
//        } else {
//            return $this->responseFormatter->sendErrorResponse(
//                "Type Not Supported",
//                [],
//                404
//            );
//        }
//    }
    public function fetchTimerProducts($response, $customerId)
    {
        $timerItems = [];
        foreach ($response as $key => $value) {
            if (in_array($key, ['data', 'sms', 'voice'])) {
                $timerItems[$key] = collect($value)->map(function ($item) {
                    if (explode('|', $item->balanceName)[0] === 'TIMER' && isset($item->product)) {
                        return $item->product->code;
                    }
                });
            }
        }

        if (count($timerItems)) {
            $subscriptionResponse = $this->get($this->getSubscriptionUrl($customerId));
            $subscriptionResponse = json_decode($subscriptionResponse['response'], true);
            $filteredData = [];
            foreach ($subscriptionResponse as $data) {
                foreach ($timerItems as $key => $item) {
                    if (in_array($data['code'], json_decode($item)) && !is_null($data['deactivatedDateTime'])) {
                        $filteredData[$key][$data['code']] = $data['deactivatedDateTime'];
                    }
                }
            }
            return $filteredData;
        }

        return [];
    }

    /**
     * @param $response
     * @param $customer
     * @param $timerProducts
     * @param $customerAvailableProducts
     * @return mixed
     */
    private function getPrepaidAllBalance($response, $customer)
    {
        $allBalance['balance'] = $this->getMainBalance($response, $customer);
        $allBalance['internet'] = $this->getBalance($response, "data");
        $allBalance['sms'] = $this->getBalance($response, "sms");
        $allBalance['minute'] = $this->getBalance($response, "voice");
        $allBalance['package'] = Customer::package($customer);
        return $allBalance;
    }

    /**
     * @param $type
     * @param $response
     * @param $customer
     * @return JsonResponse
     */
    private function getPrepaidDetails($type, $response, $customer)
    {
        if (isset($response->error)) {
            return $this->responseFormatter->sendErrorResponse(
                $response->message,
                [],
                $response->status
            );
        }

        if ($type == 'all') {
            return $this->responseFormatter->sendSuccessResponse(
                $this->getPrepaidAllBalance($response, $customer),
                'All Balance Details'
            );
        } elseif ($type == 'internet') {
            return $this->responseFormatter->sendSuccessResponse(
                $this->getBalance($response, "data"),
                'Internet Balance Details'
            );
        } elseif ($type == 'sms') {
            return $this->responseFormatter->sendSuccessResponse(
                $this->getBalance($response, "sms"),
                'SMS Balance Details'
            );
        } elseif ($type == 'minutes') {
            return $this->responseFormatter->sendSuccessResponse(
                $this->getBalance($response, "voice"),
                'Talk Time Balance Details'
            );
        } elseif ($type == 'balance') {
            return $this->responseFormatter->sendSuccessResponse(
                $this->getMainBalance($response, $customer),
                'Main Balance Details'
            );
        } else {
            return $this->responseFormatter->sendErrorResponse("Type Not Supported", [], 404);
        }
    }

    /**
     * @param $response
     * @return array
     */
    private function getPostpaidMainBalance($response)
    {
        $local_balance = collect($response)->where('billingAccountType', '=', 'LOCAL')->first();
        $local = [
            'total_outstanding' => $local_balance->totalOutstanding,
            'credit_limit' => $local_balance->creditLimit
        ];

        $roaming_balance = collect($response)->where('billingAccountType', '=', 'ROAMING')->first();
        $roaming = [
            'total_outstanding' => $roaming_balance->totalOutstanding,
            'credit_limit' => $roaming_balance->creditLimit
        ];

        $data = [
            'connection_type' => 'POSTPAID',
            'local_balance' => $local,
            'roaming_balance' => $roaming
        ];

        return $data;
    }

    private function checkPostPaidProductCode($daItem): bool
    {
        return strpos(strtolower($daItem->name), 'bonus') ? false : true;
    }

    /**
     * @param $response
     * @return array
     */
    private function getPostpaidInternetBalance($response)
    {
        $local_balance = collect($response)->where('billingAccountType', '=', 'LOCAL')->first();
        $usage = collect($local_balance->productUsage)->where('code', '<>', '');
        $data = [];

        foreach ($usage as $product) {
            $productCode = $product->code;
            foreach ($product->usages as $item) {
                $type = $item->serviceType;
                if ($type == 'DATA') {

                    $sms = [
                        'package_name' => isset($product->name) ? $product->name : null,
                        'total' => $item->total,
                        'remaining' => $item->left,
                        'unit' => $item->unit,
                        'expires_in' => Carbon::parse($product->deactivatedAt)->setTimezone('UTC')->toDateTimeString(),
                        'auto_renew' => false,
                        'product_code' => $this->checkPostPaidProductCode($item) ? $productCode : ""
                    ];
                    $data [] = $sms;
                }
            }
        }

        return $data;
    }

    /**
     * @param $response
     * @return array
     */
    private function getPostpaidSmsBalance($response)
    {
        $local_balance = collect($response)->where('billingAccountType', '=', 'LOCAL')->first();
        $usage = collect($local_balance->productUsage)->where('code', '<>', '');
        $data = [];
        foreach ($usage as $product) {
            $productCode = $product->code;
            foreach ($product->usages as $item) {
                $type = $item->serviceType;
                if ($type == 'SMS') {
                    $sms = [
                        'package_name' => isset($product->name) ? $product->name : null,
                        'total' => $item->total,
                        'remaining' => $item->left,
                        'unit' => $item->unit,
                        'expires_in' => Carbon::parse($product->deactivatedAt)->setTimezone('UTC')->toDateTimeString(),
                        'auto_renew' => false,
                        'product_code' => $this->checkPostPaidProductCode($item) ? $productCode : ""
                    ];
                    $data [] = $sms;
                }
            }
        }

        return $data;
    }

    /**
     * @param $response
     * @return array
     */
    private function getPostpaidTalkTimeBalance($response)
    {
        $local_balance = collect($response)->where('billingAccountType', '=', 'LOCAL')->first();
        $usage = collect($local_balance->productUsage)->where('code', '<>', '');
        $data = [];
        foreach ($usage as $product) {
            $productCode = $product->code;
            foreach ($product->usages as $item) {
                $type = $item->serviceType;
                if ($type == 'VOICE') {
                    $minutes = [
                        'package_name' => isset($product->name) ? $product->name : null,
                        'total' => $item->total,
                        'remaining' => $item->left,
                        'unit' => $item->unit,
                        'expires_in' => Carbon::parse($product->deactivatedAt)->setTimezone('UTC')->toDateTimeString(),
                        'auto_renew' => false,
                        'product_code' => $this->checkPostPaidProductCode($item) ? $productCode : ""
                    ];
                    $data [] = $minutes;
                }
            }
        }

        return $data;
    }



    /**
     * @param $response
     * @param $customer
     * @return mixed
     */
    private function getPostpaidAllBalance($response, $customer)
    {
        $allBalance['balance'] = $this->getPostpaidMainBalance($response);
        $allBalance['internet'] = $this->getPostpaidInternetBalance($response);
        $allBalance['sms'] = $this->getPostpaidSmsBalance($response);
        $allBalance['minute'] = $this->getPostpaidTalkTimeBalance($response);
        $allBalance['package'] = Customer::package($customer);
        return $allBalance;
    }

    public function getBalanceDetails($type, Request $request)
    {
        $user = $this->customerService->getAuthenticateCustomer($request);

        if (!$user) {
            return $this->responseFormatter->sendErrorResponse("User not found", [], HttpStatusCode::UNAUTHORIZED);
        }

        $customer_id = $user->customer_account_id;

        $customer_type = Customer::connectionType(Customer::find($user->id));

        if ($customer_type == 'PREPAID') {

            $response = $this->get($this->getPrepaidBalanceUrl($customer_id));

            //$response = $this->get($this->getPrepaidNewBalanceUrl($user->msisdn));

            $response = json_decode($response['response']);
            return $this->getPrepaidDetails($type, $response, $user);
        }

        if ($customer_type == 'POSTPAID') {
            $response = $this->get($this->getPostpaidBalanceUrl($customer_id));
            $response = json_decode($response['response']);
            return $this->getPostpaidDetails($type, $response, $user);
        }

        return $this->responseFormatter->sendSuccessResponse([], 'User Balance Details');
    }


    /**
     * @param $response
     * @return JsonResponse|mixed
     */
    private function preparePostpaidSummary($response)
    {
        $local_balance = collect($response)->where('billingAccountType', '=', 'LOCAL')->first();
        $balance = [
            'total_outstanding' => $local_balance->totalOutstanding,
            'credit_limit' => $local_balance->creditLimit,
            'payment_date' => isset($local_balance->nextPaymentDate) ?
                Carbon::parse($local_balance->nextPaymentDate)->setTimezone('UTC')->toDateTimeString() : null,
        ];

        $usage = collect($local_balance->productUsage)->where('code', '<>', '');

        $minutes = [];
        $sms = [];
        $internet = [];

        foreach ($usage as $product) {
            foreach ($product->usages as $item) {
                $type = $item->serviceType;
                switch ($type) {
                    case "DATA":
                        $internet ['total'][] = $item->total;
                        $internet ['remaining'][] = $item->left;
                        break;
                    case "VOICE":
                        $minutes ['total'][] = $item->total;
                        $minutes ['remaining'][] = $item->left;
                        break;
                    case "SMS":
                        $sms ['total'][] = $item->total;
                        $sms ['remaining'][] = $item->left;
                        break;
                }
            }
        }

        $data ['connection_type'] = 'POSTPAID';
        $data ['balance'] = $balance;
        $data ['minutes'] = [
            'total' => isset($minutes['total']) ? array_sum($minutes['total']) : 0,
            'remaining' => isset($minutes['remaining']) ? array_sum($minutes['remaining']) : 0,
            'unit' => 'MIN'
        ];
        $data ['internet'] = [
            'total' => isset($internet['total']) ? array_sum($internet['total']) : 0,
            'remaining' => isset($internet['remaining']) ? array_sum($internet['remaining']) : 0,
            'unit' => 'MB'
        ];
        $data ['sms'] = [
            'total' => isset($sms['total']) ? array_sum($sms['total']) : 0,
            'remaining' => isset($sms['remaining']) ? array_sum($sms['remaining']) : 0,
            'unit' => 'SMS'
        ];

        return $data;

        //return $this->responseFormatter->sendSuccessResponse($data, 'User Balance Summary');
    }

    /**
     * @param $type
     * @param $response
     * @return JsonResponse
     */
    private function getPostpaidDetails($type, $response, $customer)
    {
        if (isset($response->error)) {
            return $this->responseFormatter->sendErrorResponse(
                $response->message,
                [],
                $response->status
            );
        }

        if ($type == 'all') {
            return $this->responseFormatter->sendSuccessResponse($this->getPostpaidAllBalance($response, $customer),
                'All Balance for Postpaid');
        } elseif ($type == 'internet') {
            return $this->responseFormatter->sendSuccessResponse($this->getPostpaidInternetBalance($response),
                'DATA  Balance Details');
        } elseif ($type == 'sms') {
            return $this->responseFormatter->sendSuccessResponse($this->getPostpaidSmsBalance($response),
                'SMS  Balance Details');
        } elseif ($type == 'minutes') {
            return $this->responseFormatter->sendSuccessResponse($this->getPostpaidTalkTimeBalance($response),
                'Talk Time  Balance Details');
        } elseif ($type == 'balance') {
            return $this->responseFormatter->sendSuccessResponse($this->getPostpaidMainBalance($response),
                'Main Balance Details');
        } else {
            return $this->responseFormatter->sendErrorResponse(
                "Type Not Supported",
                [],
                404
            );
        }
    }

    /**
     * @param $response
     * @return array
     */
    private function preparePostpaidBalanceSummary($response)
    {
        $balance_data = collect($response);

        $data = [];
        $balance_data_roaming = null;
        $balance_data_local = null;
        foreach ($balance_data as $item) {

            if( $item->billingAccountType == 'ROAMING' ){

                $balance_data_roaming = $item;
            }
            elseif( $item->billingAccountType == 'LOCAL' ){

                $balance_data_local = $item;
            }
        }

        $data['balance'] = [
            'amount' => isset($balance_data_local->totalOutstanding) ? $balance_data_local->totalOutstanding : 0 ,
            'unit' => isset($balance_data_local->unit) ? $balance_data_local->unit : 'BDT',
            'expires_in' => isset($balance_data_local->nextPaymentDate) ?
                Carbon::parse($balance_data_local->nextPaymentDate)->setTimezone('UTC')->toDateTimeString() : null,
        ];

        $data['local'] = [
            'billingAccountType' => isset($balance_data_local->billingAccountType) ? $balance_data_local->billingAccountType : null,
            'totalOutstanding' => isset($balance_data_local->totalOutstanding) ? $balance_data_local->totalOutstanding : 0,
            'creditLimit' => isset($balance_data_local->creditLimit) ? $balance_data_local->creditLimit : 0,
            'overPayment' => isset($balance_data_local->overPayment) ? $balance_data_local->overPayment : 0,
            'nextPaymentDate' => isset($balance_data_local->nextPaymentDate) ? Carbon::parse($balance_data_local->nextPaymentDate)->setTimezone('UTC')->toDateTimeString() : null,
        ];

        $usage = collect($balance_data_local->productUsage)->where('code', '<>', '');

        $minutes = [];
        $sms = [];
        $internet = [];
        $local_product_usage = [];
        foreach ($usage as $product) {
            foreach ($product->usages as $item) {
                $type = $item->serviceType;
                switch ($type) {
                    case "DATA":
                        $internet ['total'][] = $item->total;
                        $internet ['remaining'][] = $item->left;
                        break;
                    case "VOICE":
                        $minutes ['total'][] = $item->total;
                        $minutes ['remaining'][] = $item->left;
                        break;
                    case "SMS":
                        $sms ['total'][] = $item->total;
                        $sms ['remaining'][] = $item->left;
                        break;
                }
            }
        }

        $data ['connection_type'] = 'POSTPAID';
       // $data ['balance'] = $balance;
        $local_product_usage ['minutes'] = [
            'total' => isset($minutes['total']) ? array_sum($minutes['total']) : 0,
            'remaining' => isset($minutes['remaining']) ? array_sum($minutes['remaining']) : 0,
            'unit' => 'MIN'
        ];
        $local_product_usage ['internet'] = [
            'total' => isset($internet['total']) ? array_sum($internet['total']) : 0,
            'remaining' => isset($internet['remaining']) ? array_sum($internet['remaining']) : 0,
            'unit' => 'MB'
        ];
        $local_product_usage ['sms'] = [
            'total' => isset($sms['total']) ? array_sum($sms['total']) : 0,
            'remaining' => isset($sms['remaining']) ? array_sum($sms['remaining']) : 0,
            'unit' => 'SMS'
        ];


        $data['local']['product_usages'] = $local_product_usage;

        $data['roaming'] = [
            'billingAccountType' => isset($balance_data_roaming->billingAccountType) ? $balance_data_roaming->billingAccountType : null,
            'totalOutstanding' => isset($balance_data_roaming->totalOutstanding) ? $balance_data_roaming->totalOutstanding : 0,
            'creditLimit' => isset($balance_data_roaming->creditLimit) ? $balance_data_roaming->creditLimit : 0,
            'overPayment' => isset($balance_data_roaming->overPayment) ? $balance_data_roaming->overPayment : 0,
            'nextPaymentDate' => isset($balance_data_roaming->overPayment) ? Carbon::parse($balance_data_roaming->nextPaymentDate)->setTimezone('UTC')->toDateTimeString() : null,
        ];

        $roming_product_usage = [];
        if( !empty($balance_data_roaming) && !empty($balance_data_roaming->productUsage) ){
            foreach ($balance_data_roaming->productUsage as $roaming_product ) {

                if( !empty($roaming_product->code) && !empty($roaming_product->commercialName && !empty($roaming_product->usages) )  ){

                    foreach ($roaming_product->usages as $usages) {

                        if( $usages->serviceType == 'VOICE' ){

                            $roming_product_usage['minutes'] = [
                                'total' => !empty($usages->total) ?  $usages->total : 0,
                                'remaining' => !empty($usages->left) ?  $usages->left : 0,
                                'unit' => 'MIN'
                            ];

                        }
                        elseif( $usages->serviceType == 'SMS' ){

                            $roming_product_usage['sms'] = [
                                'total' => !empty($usages->total) ?  $usages->total : 0,
                                'remaining' => !empty($usages->left) ?  $usages->left : 0,
                                'unit' => 'SMS'
                            ];

                        }
                        elseif( $usages->serviceType == 'DATA' ){
                            $roming_product_usage['internet'] = [
                                'total' => !empty($usages->total) ?  $usages->total : 0,
                                'remaining' => !empty($usages->left) ?  $usages->left : 0,
                                'unit' => 'MB'
                            ];

                        }

                    }
                }

            }
        }

        $data['roaming']['product_usages'] = $roming_product_usage;

        # Default local data sending for postpaid
        $default_minutes = !empty($local_product_usage['minutes']) ? $local_product_usage['minutes'] : ( !empty($roming_product_usage['minutes']) ?  $roming_product_usage['minutes'] : 0 );

        $default_sms = !empty($local_product_usage['sms']) ? $local_product_usage['sms'] : ( !empty($roming_product_usage['sms']) ?  $roming_product_usage['sms'] : 0 );

        $default_internet = !empty($local_product_usage['internet']) ? $local_product_usage['internet'] : ( !empty($roming_product_usage['internet']) ?  $roming_product_usage['internet'] : 0 );

        $data['minutes'] = $default_minutes;
        $data['sms'] = $default_sms;
        $data['internet'] = $default_internet;

        return $data;
    }

    /**
     * [prepareBalanceSummaryPostpaid Balance summery for postpaid]
     * @param  [mixed] $response    [description]
     * @param  [int] $customer_id [description]
     * @return [mixed]              [description]
     */
    private function prepareBalanceSummaryPostpaid($response, $customer_id)
    {
        $balance_data = collect($response);

        $data = [];
        $balance_data_roaming = null;
        $balance_data_local = null;
        foreach ($balance_data as $item) {

            if( $item->billingAccountType == 'ROAMING' ){

                $balance_data_roaming = $item;
            }
            elseif( $item->billingAccountType == 'LOCAL' ){

                $balance_data_local = $item;
            }
        }

        $data['balance'] = [
            'amount' => isset($balance_data_local->totalOutstanding) ? $balance_data_local->totalOutstanding : 0 ,
            'unit' => isset($balance_data_local->unit) ? $balance_data_local->unit : 'BDT',
            'expires_in' => isset($balance_data_local->nextPaymentDate) ?
                Carbon::parse($balance_data_local->nextPaymentDate)->setTimezone('UTC')->toDateTimeString() : null,
            // 'loan' => [
            //     'is_eligible' => $is_eligible_to_loan,
            //     'amount'      => ($is_eligible_to_loan) ? 30 : 0
            // ]
        ];

        $data['local'] = [
            'billingAccountType' => isset($balance_data_local->billingAccountType) ? $balance_data_local->billingAccountType : null,
            'totalOutstanding' => isset($balance_data_local->totalOutstanding) ? $balance_data_local->totalOutstanding : 0,
            'creditLimit' => isset($balance_data_local->creditLimit) ? $balance_data_local->creditLimit : 0,
            'overPayment' => isset($balance_data_local->overPayment) ? $balance_data_local->overPayment : 0,
            'nextPaymentDate' => isset($balance_data_local->nextPaymentDate) ? Carbon::parse($balance_data_local->nextPaymentDate)->setTimezone('UTC')->toDateTimeString() : null,
        ];

        $local_product_usage = [];
        if( !empty($balance_data_local) && !empty($balance_data_local->productUsage) ){
            foreach ($balance_data_local->productUsage as $local_product ) {

                if( !empty($local_product->code) && !empty($local_product->commercialName && !empty($local_product->usages) )  ){

                    foreach ($local_product->usages as $usages) {

                        if( $usages->serviceType == 'VOICE' ){

                            $local_product_usage['minutes'] = [
                                'total' => !empty($usages->total) ?  $usages->total : 0,
                                'remaining' => !empty($usages->left) ?  $usages->left : 0,
                                'unit' => 'MIN'
                            ];

                        }
                        elseif( $usages->serviceType == 'SMS' ){

                            $local_product_usage['sms'] = [
                                'total' => !empty($usages->total) ?  $usages->total : 0,
                                'remaining' => !empty($usages->left) ?  $usages->left : 0,
                                'unit' => 'SMS'
                            ];

                        }
                        elseif( $usages->serviceType == 'DATA' ){
                            $local_product_usage['internet'] = [
                                'total' => !empty($usages->total) ?  $usages->total : 0,
                                'remaining' => !empty($usages->left) ?  $usages->left : 0,
                                'unit' => 'MB'
                            ];

                        }

                    }
                }

            }
        }

        $data['local']['product_usages'] = $local_product_usage;

        $data['roaming'] = [
            'billingAccountType' => isset($balance_data_roaming->billingAccountType) ? $balance_data_roaming->billingAccountType : null,
            'totalOutstanding' => isset($balance_data_roaming->totalOutstanding) ? $balance_data_roaming->totalOutstanding : 0,
            'creditLimit' => isset($balance_data_roaming->creditLimit) ? $balance_data_roaming->creditLimit : 0,
            'overPayment' => isset($balance_data_roaming->overPayment) ? $balance_data_roaming->overPayment : 0,
            'nextPaymentDate' => isset($balance_data_roaming->overPayment) ? Carbon::parse($balance_data_roaming->nextPaymentDate)->setTimezone('UTC')->toDateTimeString() : null,
        ];

        $roming_product_usage = [];
        if( !empty($balance_data_roaming) && !empty($balance_data_roaming->productUsage) ){
            foreach ($balance_data_roaming->productUsage as $roaming_product ) {

                if( !empty($roaming_product->code) && !empty($roaming_product->commercialName && !empty($roaming_product->usages) )  ){

                    foreach ($roaming_product->usages as $usages) {

                        if( $usages->serviceType == 'VOICE' ){

                            $roming_product_usage['minutes'] = [
                                'total' => !empty($usages->total) ?  $usages->total : 0,
                                'remaining' => !empty($usages->left) ?  $usages->left : 0,
                                'unit' => 'MIN'
                            ];

                        }
                        elseif( $usages->serviceType == 'SMS' ){

                            $roming_product_usage['sms'] = [
                                'total' => !empty($usages->total) ?  $usages->total : 0,
                                'remaining' => !empty($usages->left) ?  $usages->left : 0,
                                'unit' => 'SMS'
                            ];

                        }
                        elseif( $usages->serviceType == 'DATA' ){
                            $roming_product_usage['internet'] = [
                                'total' => !empty($usages->total) ?  $usages->total : 0,
                                'remaining' => !empty($usages->left) ?  $usages->left : 0,
                                'unit' => 'MB'
                            ];

                        }

                    }
                }

            }
        }

        $data['roaming']['product_usages'] = $roming_product_usage;

        # Default local data sending for postpaid
        $default_minutes = !empty($local_product_usage['minutes']) ? $local_product_usage['minutes'] : ( !empty($roming_product_usage['minutes']) ?  $roming_product_usage['minutes'] : 0 );

        $default_sms = !empty($local_product_usage['sms']) ? $local_product_usage['sms'] : ( !empty($roming_product_usage['sms']) ?  $roming_product_usage['sms'] : 0 );

        $default_internet = !empty($local_product_usage['internet']) ? $local_product_usage['internet'] : ( !empty($roming_product_usage['internet']) ?  $roming_product_usage['internet'] : 0 );

        $data['minutes'] = $default_minutes;
        $data['sms'] = $default_sms;
        $data['internet'] = $default_internet;

        return $data;
    }

    private function getPrepaidBalanceUrl($customer_id)
    {
        return self::BALANCE_API_ENDPOINT . '/' . $customer_id . '/prepaid-balances' . '?sortType=SERVICE_TYPE';
    }

    public function getPrepaidBalance($customer_id)
    {
        $response = $this->get($this->getPrepaidBalanceUrl($customer_id));
        $response = json_decode($response['response']);

        if (isset($response->error)) {
            return $this->responseFormatter->sendErrorResponse(
                'Currently Service Unavailable. Please,try again later',
                [
                    'message' => 'Currently Service Unavailable. Please,try again later',
                ],
                500
            );
        }

        $balance_data = collect($response->money);

        $main_balance = $balance_data->first(function ($item) {
            return $item->type == 'MAIN';
        });

        return isset($main_balance->amount) ? $main_balance->amount : 0;
    }

    private function getPostpaidBalanceUrl($customer_id)
    {
        return self::BALANCE_API_ENDPOINT . '/' . $customer_id . '/postpaid-info';
    }

    public function getPostpaidBalance($customer_id)
    {
        $response = $this->get($this->getPostpaidBalanceUrl($customer_id));
        $response = json_decode($response['response']);

        if (isset($response->error)) {
            return $this->responseFormatter->sendErrorResponse(
                'Currently Service Unavailable. Please,try again later',
                [
                    'message' => 'Currently Service Unavailable. Please,try again later',
                ],
                500
            );
        }

        $local_balance = collect($response)->where('billingAccountType', '=', 'LOCAL')->first();

        return ($local_balance->creditLimit - $local_balance->totalOutstanding);
    }
}
