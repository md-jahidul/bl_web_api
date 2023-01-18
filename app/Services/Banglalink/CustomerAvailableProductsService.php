<?php

namespace App\Services\Banglalink;

use App\Enums\HttpStatusCode;
use App\Models\MyBlProduct;
use App\Repositories\CustomerRepository;
use App\Services\ApiBaseService;
use App\Services\CustomerService;
use App\Services\IdpIntegrationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class CustomerAvailableProductsService extends BaseService
{
    protected $responseFormatter;
    protected const CUSTOMER_INFO_API_ENDPOINT = "/customer-information/customer-information";
    /**
     * @var CustomerRepository
     */
    protected $customerRepository;

    /**
     * @var CustomerService
     */
    protected $customerService;

    /**
     * CustomerAvailableProductsService constructor.
     * @param CustomerService $customerService
     */
    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
        $this->responseFormatter = new ApiBaseService();
        $this->customerRepository = new CustomerRepository();
    }


    private function getAvailableProductUrl($customer_id): string
    {
        $channelName = env('CHANNEL_NAME_WEV', "website");
        return self::CUSTOMER_INFO_API_ENDPOINT . '/' . $customer_id . '/available-products?channel=' . $channelName;
    }

    public function getAvailableProductsByCustomer($customer_id)
    {
        $products = Redis::get('available_products:' . $customer_id);

        if (!$products) {
            $response = $this->get($this->getAvailableProductUrl($customer_id));
            $products = json_decode($response['response']);
            $collection = collect($products)->groupBy('templateType');

            $available_products = [];

            foreach ($collection as $key => $item) {
                foreach ($item as $val) {
                    $available_products [] = $val->code;
                }
            }
            Redis::setex('available_products:' . $customer_id, 60 * 60 * 24, json_encode($available_products));

            return $available_products;

        } elseif (count(json_decode($products)) == 0) {

            $response = $this->get($this->getAvailableProductUrl($customer_id));
            $products = json_decode($response['response']);
            $collection = collect($products)->groupBy('templateType');

            $available_products = [];

            foreach ($collection as $key => $item) {
                foreach ($item as $val) {
                    $available_products [] = $val->code;
                }
            }
            Redis::setex('available_products:' . $customer_id, 60 * 60 * 24, json_encode($available_products));

            return $available_products;

        }

        return json_decode($products, true);
    }
}
