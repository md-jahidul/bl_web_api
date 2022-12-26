<?php

namespace App\Services\Banglalink;

use App\Enums\HttpStatusCode;
use App\Repositories\CustomerRepository;
use App\Services\ApiBaseService;
use App\Services\IdpIntegrationService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CustomerPackageService extends BaseService
{
    protected $responseFormatter;
    protected const CUSTOMER_PACKAGE_API_ENDPOINT = "/customer-information/customer-information";
    /**
     * @var CustomerRepository
     */
    protected $customerRepository;

    public function __construct()
    {
        $this->responseFormatter = new ApiBaseService();
        $this->customerRepository = new CustomerRepository();
    }

    private function getPackageInfoUrl($customer_id)
    {
        return self::CUSTOMER_PACKAGE_API_ENDPOINT . '/' . $customer_id . '/packages';
    }

    public function getPackageInfo($customer_id)
    {
        $response = $this->get($this->getPackageInfoUrl($customer_id));
        $response = json_decode($response['response']);
        $package = [];
        if (!$response) {
            return $package;
        }
        if (isset($response->error)) {
            return $package;
        }

        $package = [
            'title' => $response->name,
            'code' => $response->code,
        ];

        return $package;
    }
}
