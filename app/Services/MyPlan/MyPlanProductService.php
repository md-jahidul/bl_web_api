<?php

namespace App\Services\MyPlan;

use App\Enums\HttpStatusCode;
use App\Repositories\MyPlanProductRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use App\Services\Banglalink\BaseService;
use App\Http\Resources\MyPlanProductResource;
use App\Services\Banglalink\CustomerAvailableProductsService;

class MyPlanProductService extends BaseService
{

    const PRODUCT_CATALOG = '/customer-information/customer-information/product-catalog/';

    protected $myBlPlanProductRepository;

    /**
     * @var CustomerAvailableProductsService
     */
    protected $customerAvailableProductsService;

    public function __construct(
        MyPlanProductRepository $myPlanProductRepository,
        CustomerAvailableProductsService $customerAvailableProductsService
    ) {
        $this->myBlPlanProductRepository = $myPlanProductRepository;
        $this->customerAvailableProductsService = $customerAvailableProductsService;
    }

    public function getMyPlanProducts()
    {
        $redis_key = "my_plan_prepaid_products";
        $redis_ttl = 24*60*60;

        $products = json_decode(Redis::get($redis_key), true);
        if (!$products) {
            $products = $this->myBlPlanProductRepository->findBy(['is_active' => 1, 'sim_type' => 'prepaid']);
            $available_products =  $this->getAvailablePlanProducts();
            $filterdProducts = $products->whereIn('product_code', $available_products);
            $products = $this->getPlansFormatterData($filterdProducts);
            Redis::setex($redis_key, $redis_ttl, json_encode($products));
            return $products;
        }
        return $products;
    }


    private function getAvailablePlanProducts()
    {
        $available_products = [];

        $channel = "BLFlexiPlan";
        $packageID = 1;
        $url = self::PRODUCT_CATALOG . $packageID . '?channel=' . $channel;

        $response = $this->getV2($url);

        $products = json_decode($response['response'], true);
        if ($response['status_code'] == HttpStatusCode::SUCCESS) {
            foreach ($products as $product) {
                $available_products[] = $product['code'];
            }
        } else {
            Log::channel('MyPlan')->info('Failed to get available products from APIHUB API', [
                'response' => $response['response'],
                'status_code' => $response['status_code'],
            ]);
        }

        return $available_products;
    }

    private function getPlansFormatterData($products)
    {
        $validity = [];
        $internet = [];
        $sms = [];
        $minutes = [];
        $default = null;
        $plans = MyPlanProductResource::collection($products);

        foreach($products as $product) {
            $validity[] = $product->validity;

            $dataVolume = ($product->data_volume_unit === 'gb') ? $product->data_volume * 1024 : $product->data_volume;
            if ($product->data_volume !== null) {
                $internet[] = $dataVolume;
            }

            if ($product->sms_volume !== null) {
                $sms[] = $product->sms_volume;
            }

            if ($product->minute_volume !== null) {
                $minutes[] = $product->minute_volume;
            }

            if ($product->is_default == 1) {
                $default = (object) [
                    'validity' => $product->validity,
                    'internet' => $dataVolume,
                    'minutes' => $product->minute_volume,
                    'sms' => $product->sms_volume
                ];
            }
        }

        $validity = array_values(array_unique($validity));
        $internet = array_values(array_unique($internet));
        $sms = array_values(array_unique($sms));
        $minutes = array_values(array_unique($minutes));

        sort($validity);
        sort($internet);
        sort($sms);
        sort($minutes);

        $products = [
            "validity" => $validity,
            "internet" => $internet,
            "sms" => $sms,
            "minutes" => $minutes,
            "default" => $default,
            "plans" => $plans
        ];

        return $products;
    }
}
