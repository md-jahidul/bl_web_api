<?php

namespace App\Services\Banglalink;

use App\Enums\HttpStatusCode;
use App\Enums\MyBlAppSettingsKey;
use App\Models\Customer;
use App\Models\MyBlAppSettings;
use App\Models\MyBlProduct;
use App\Models\ProductCore;
use App\Repositories\CustomerRepository;
use App\Services\ApiBaseService;
use App\Services\CustomerService;
use App\Services\IdpIntegrationService;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Class ProductLoanService
 * @package App\Services\Banglalink
 */
class ProductLoanService extends BaseService
{
    protected $responseFormatter;
    protected const CUSTOMER_INFO_API_ENDPOINT = "/customer-information/customer-information";
    protected const BALANCE_API_ENDPOINT = "/customer-information/customer-information";
    /**
     * @var CustomerRepository
     */
    protected $customerRepository;

    /**
     * @var CustomerService
     */
    protected $customerService;
    protected $balanceService;
    private $blCustomerService;

    /**
     * ProductLoanService constructor.
     * @param CustomerService $customerService
     * @param BanglalinkCustomerService $banglalinkCustomerService
     */
    public function __construct(CustomerService $customerService,    BanglalinkCustomerService $banglalinkCustomerService)
    {

        //dd($balanceService);
        $this->customerService = $customerService;
        $this->blCustomerService = $banglalinkCustomerService;
        $this->responseFormatter = new ApiBaseService();
        $this->customerRepository = new CustomerRepository();
    }

    private function getLoanInfoUrl($customer_id)
    {
        return self::CUSTOMER_INFO_API_ENDPOINT . '/' . $customer_id . '/available-loan-products';
    }

    private function getPrepaidBalanceUrl($customer_id)
    {
        return self::BALANCE_API_ENDPOINT . '/' . $customer_id . '/prepaid-balances' . '?sortType=SERVICE_TYPE';
    }

    public function getAvailableLoansByCustomer($customer_id)
    {
        $response = $this->get($this->getLoanInfoUrl($customer_id));
        if ($response['status_code'] == 200) {
            return json_decode($response['response'], true);
        }
        throw new \RuntimeException("Internal service error", $response['status_code']);
    }

    private function getMainBalance($customer_id)
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

        return  isset($main_balance->amount) ? $main_balance->amount : 0;
    }

    public function hasMALoan($customer_id)
    {
        $response = $this->get($this->getLoanInfoUrl($customer_id));

        $data = json_decode($response['response']);
        $formatted_data = $this->prepareLoanData($data);
        $has_ma_loan = false;
        $amount = 0;
        foreach ($formatted_data as $val) {
            if ($val['type'] == 'balance') {
                $has_ma_loan = true;
                $amount = $val['amount'];
                break;
            }
        }

        return [
            'is_eligible' => $has_ma_loan,
            'amount' => $amount
        ];
    }

    public function getLoanInfo($request, $loanType)
    {
        $user = $this->customerService->getCustomerDetails($request);

        $customerInfo = $this->blCustomerService->getCustomerInfoByNumber(8801932287502);

//        $customerInfo = $this->blCustomerService->getCustomerInfoByNumber($user->msisdn);

        $customer_type = $customerInfo->getData()->data->connectionType;
        $customer_account_id = $customerInfo->getData()->data->package->customerId;

        if (!$user) {
            return $this->responseFormatter->sendErrorResponse("User not found", [], HttpStatusCode::UNAUTHORIZED);
        }

        // Customer type check
        if ($customer_type == 'POSTPAID') {
            return $this->responseFormatter->sendErrorResponse(
                'POSTPAID_USER',
                [
                    'message' => 'Emergency Balance is not eligible for Postpaid user',
                    'hint'    => 'Postpaid user not eligible for Emergency balance'
                ],
                400
            );
        }
        $balance = $this->getMainBalance($customer_account_id);
        $min_amount = 10;

        // Balance Check more than 10 tk
        if ($loanType == "balance"){
            if ($balance > $min_amount) {
                return $this->responseFormatter->sendSuccessResponse(
                    [],
                    "NOT_ELIGIBLE"
                );
            }
        }
        $loanProducts = $this->getAvailableLoansByCustomer($customer_account_id);

        $availableLoanProducts = $this->prepareLoanData($loanProducts, $loanType);

        if ($availableLoanProducts){
            return $this->responseFormatter->sendSuccessResponse($availableLoanProducts, 'Available loan products');
        }
        return $this->responseFormatter->sendSuccessResponse(
            [],
            "NOT_AVAILED"
        );
    }

    public function prepareLoanData($loanProducts, $loanType)
    {
        $availableLoanProducts = [];
        foreach ($loanProducts as $loan) {
            $product = ProductCore::where('product_code', $loan['code'])->first();
            if (empty($product)) {
                return $this->responseFormatter->sendErrorResponse([], "Load Product not found" . " Product code = " . $loan['code']);
            }
            $product = array(
                'product_code' => $product->product_code,
                'type' => ($product->content_type == 'data loan') ? 'internet' : 'balance',
                'title' => $product->name,
                'internet_volume_mb' => $product->internet_volume_mb,
                'price_tk' => $product->mrp_price,
                'validity' => $product->validity,
                'validity_unit' => $product->validity_unit
            );

            if ($loanType == $product['type']) {
                array_push($availableLoanProducts, $product);
            }
        }
        return $availableLoanProducts;
    }
}
