<?php

namespace App\Services\Banglalink;

use App\Enums\HttpStatusCode;
use App\Exceptions\AmarOfferBuyException;
use App\Exceptions\TokenInvalidException;
use App\Repositories\CustomerRepository;
use App\Services\ApiBaseService;
use App\Services\CustomerService;
use App\Services\IdpIntegrationService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionProductService extends BaseService
{
    /**
     * @var ApiBaseService
     */
    public $responseFormatter;
    protected const SUBSCRIPTION_PRODUCT_API_ENDPOINT = "/customer-information/customer-information";
    /**
     * @var CustomerService
     */
    protected $customerService;

    public function __construct(ApiBaseService $apiBaseService, CustomerService $customerService)
    {
        $this->responseFormatter = $apiBaseService;
        $this->customerService = $customerService;
    }
    private function getPrepaidBalanceUrl($customer_id)
    {
        return self::SUBSCRIPTION_PRODUCT_API_ENDPOINT . '/' . $customer_id . '/subscription-products';
    }


    public function getSubscriptionProducts($customer_id)
    {
        $response_data = $this->get($this->getPrepaidBalanceUrl($customer_id));

        return  json_decode($response_data['response'], true);
    }

}
