<?php

namespace App\Services\Banglalink;

use App\Enums\HttpStatusCode;
use App\Repositories\CustomerRepository;
use App\Services\ApiBaseService;
use App\Services\IdpIntegrationService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BalanceService extends BaseService
{
    /**
     * @var ApiBaseService
     */
    public $responseFormatter;
    protected const BALANCE_API_ENDPOINT = "/customer-information/customer-information";
    protected const MINIMUM_BALANCE_FOR_LOAN = 20;
    /**
     * @var CustomerRepository
     */
    protected $customerRepository;

    public function __construct()
    {
        $this->responseFormatter = new ApiBaseService();
        $this->customerRepository = new CustomerRepository();
    }

    private function getBalanceUrl($customer_id)
    {
        return self::BALANCE_API_ENDPOINT . '/' . $customer_id . '/prepaid-balances' . '?sortType=SERVICE_TYPE';
    }

    private function getAuthenticateUser($request)
    {
        $bearerToken = ['token' => $request->header('authorization')];


        $response = IdpIntegrationService::tokenValidationRequest($bearerToken);

        $data = json_decode($response, true);


        if ($data['token_status'] != 'Valid') {
            return $this->responseFormatter->sendErrorResponse("Token is Invalid", [], HttpStatusCode::UNAUTHORIZED);
        }

        $user = $this->customerRepository->getCustomerInfoByPhone($data['user']['mobile']);

        return $user;
    }

    private function isEligibleToLoan($balance)
    {
        return random_int(0, 1) && $balance < self::MINIMUM_BALANCE_FOR_LOAN ? true : false;
    }

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

        return $this->responseFormatter->sendSuccessResponse($data, 'User Balance Summary');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getBalanceSummary($customerAccountId)
    {
        $customer_id = $customerAccountId;
        $response = $this->get($this->getBalanceUrl($customer_id));
        $response = json_decode($response['response']);

        if (isset($response->error)) {
            return $this->responseFormatter->sendErrorResponse(
                $response->message,
                [],
                $response->status
            );
        }

        return $this->prepareBalanceSummary($response, $customer_id);
    }

    private function getInternetBalance($response)
    {
        $internet_data = collect($response->data);

        $internet = $internet_data->filter(function ($item) {
            return $item->serviceType == 'DATA';
        });

        $data = [];
        foreach ($internet as $item) {
            $data [] = [
                'package_name' => isset($item->product->name) ? $item->product->name : null,
                'total' => $item->totalAmount,
                'remaining' => $item->amount,
                'unit' => $item->unit,
                'expires_in' => Carbon::parse($item->expiryDateTime)->setTimezone('UTC')->toDateTimeString(),
                'auto_renew' => false
            ];
        }

        return $this->responseFormatter->sendSuccessResponse($data, 'Internet  Balance Details');
    }

    private function getSmsBalance($response)
    {
        $sms = collect($response->sms);
        $data = [];
        foreach ($sms as $item) {
            $data [] = [
                'package_name' => isset($item->product->name) ? $item->product->name : null,
                'total' => $item->totalAmount,
                'remaining' => $item->amount,
                'unit' => $item->unit,
                'expires_in' => Carbon::parse($item->expiryDateTime)->setTimezone('UTC')->toDateTimeString(),
                'auto_renew' => false
            ];
        }

        return $this->responseFormatter->sendSuccessResponse($data, 'SMS  Balance Details');
    }

    private function getTalkTimeBalance($response)
    {
        $talk_time = collect($response->voice);

        $data = [];
        foreach ($talk_time as $item) {
            $data [] = [
                'package_name' => isset($item->product->name) ? $item->product->name : null,
                'total' => $item->totalAmount,
                'remaining' => $item->amount,
                'unit' => $item->unit,
                'expires_in' => Carbon::parse($item->expiryDateTime)->setTimezone('UTC')->toDateTimeString(),
                'auto_renew' => false
            ];
        }

        return $this->responseFormatter->sendSuccessResponse($data, 'Talk Time  Balance Details');
    }

    private function getMainBalance($response)
    {
        $balance_data = collect($response->money);

        $main_balance = $balance_data->first(function ($item) {
            return $item->type == 'MAIN';
        });

        $data = [
            'remaining_balance' => [
                'amount' => isset($main_balance->amount) ? $main_balance->amount : 0,
                'currency' => 'Tk.',
                'expires_in' => isset($main_balance->expiryDateTime) ?
                    Carbon::parse($main_balance->expiryDateTime)->setTimezone('UTC')->toDateTimeString() : null
            ],
            'roaming_balance' => [
                'amount' => 0,
                'currency' => 'USD',
                'expires_in' => null
            ]
        ];


        return $this->responseFormatter->sendSuccessResponse($data, 'Main Balance Details');
    }

    public function getBalanceDetails($type, Request $request)
    {
        $user = $this->getAuthenticateUser($request);

        if (!$user) {
            return $this->responseFormatter->sendErrorResponse("User not found", [], HttpStatusCode::UNAUTHORIZED);
        }

        $customer_id = ($user->customer_account_id) ? $user->customer_account_id : 8494;
        $response = $this->get($this->getBalanceUrl($customer_id));
        $response = json_decode($response['response']);

        if (isset($response->error)) {
            return $this->responseFormatter->sendErrorResponse(
                $response->message,
                [],
                $response->status
            );
        }

        if ($type == 'internet') {
            return $this->getInternetBalance($response);
        } elseif ($type == 'sms') {
            return $this->getSmsBalance($response);
        } elseif ($type == 'minutes') {
            return $this->getTalkTimeBalance($response);
        } elseif ($type == 'balance') {
            return $this->getMainBalance($response);
        } else {
            return $this->responseFormatter->sendErrorResponse(
                "Type Not Supported",
                [],
                404
            );
        }
    }
}
