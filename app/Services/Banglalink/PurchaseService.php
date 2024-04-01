<?php

namespace App\Services\Banglalink;

use App\Enums\HttpStatusCode;
use App\Repositories\CustomerRepository;
use App\Services\ApiBaseService;
use App\Services\CustomerService;
use App\Services\IdpIntegrationService;
use Illuminate\Http\Request;

/**
 * Class PurchaseService
 * @package App\Services\Banglalink
 */
class PurchaseService extends BaseService
{
    /**
     * @var ApiBaseService
     */
    protected $apiBaseService;

    /**
     * @var CustomerService
     */
    protected $customerService;

    /**
     * @var CustomerRepository
     */
    protected $customerRepository;

    /**
     * @var BanglalinkCustomerService
     */
    protected $blCustomerService;

    protected const PURCHASE_ENDPOINT = "/provisioning/provisioning/purchase";


    /**
     * FnfService constructor.
     * @param ApiBaseService $apiBaseService
     * @param CustomerService $customerService
     * @param BanglalinkCustomerService $blCustomerService
     */
    public function __construct(ApiBaseService $apiBaseService, CustomerService $customerService,
                                BanglalinkCustomerService $blCustomerService, CustomerRepository $customerRepository)
    {
        $this->apiBaseService = $apiBaseService;
        $this->customerService = $customerService;
        $this->blCustomerService = $blCustomerService;
        $this->customerRepository = $customerRepository;
    }


    /**
     * Purchase item or offer
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function purchaseItem(Request $request)
    {

        $bearerToken = ['token' => $request->header('authorization')];

        $response = IdpIntegrationService::tokenValidationRequest($bearerToken);

        $idpData = json_decode($response['data']);

        if ($response['http_code'] != 200 || $idpData->token_status != 'Valid') {
            return $this->apiBaseService->sendErrorResponse("Token is Invalid", [], HttpStatusCode::UNAUTHORIZED);
        }

        $customer = $this->customerRepository->getCustomerInfoByPhone($idpData->user->mobile);
        if (!$customer)
            return $this->apiBaseService->sendErrorResponse("Customer not found", [], HttpStatusCode::UNAUTHORIZED);

        $mobile = "88" . $customer->phone;
        $product_code = $request->input('product_code');

        $param = [
            "channel" => env('PURCHASE_CHANNEL_NAME', 'website'),
            'id' => $product_code,
            'msisdn' => $mobile
        ];

        $result = $this->post(self::PURCHASE_ENDPOINT, $param);

        if ($result['status_code'] == 200) {
            return $this->apiBaseService->sendSuccessResponse(
                json_decode($result['response'], true),
                "Purchase request successfully received and under process",
                [],
                HttpStatusCode::SUCCESS
            );
        }

        if ($result['status_code'] != 500) {
            return $this->apiBaseService->sendErrorResponse('Purchase failed', "Product code couldn't be performed for purchase", $result['status_code']);
        }

        return $this->apiBaseService->sendErrorResponse(
            "Internal service error",
            [],
            HttpStatusCode::INTERNAL_ERROR
        );
    }

}
