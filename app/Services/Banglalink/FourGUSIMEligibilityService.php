<?php

namespace App\Services\Banglalink;

use App\Enums\HttpStatusCode;

use App\Exceptions\AmarOfferBuyException;
use App\Exceptions\IdpAuthException;

use App\Repositories\AmarOfferDetailsRepository;
use App\Services\ApiBaseService;
use App\Services\CustomerService;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FourGUSIMEligibilityService extends BaseService
{
    /**
     * @var ApiBaseService
     */
    public $productService;
    public $responseFormatter;
    /**
     * @var BanglalinkCustomerService
     */
    protected $blCustomerService;

    protected const CUSTOMER_ENDPOINT   = "/customer-information/customer-information/";

    public function __construct(
        ApiBaseService $apiBaseService,
        BanglalinkCustomerService $banglalinkCustomerService
    ) {
        $this->blCustomerService = $banglalinkCustomerService;
        $this->responseFormatter = $apiBaseService;
    }

    protected $eligibilityMessage = [
        '4g_usim' => [
            'message' => 'Dear Customer, you are already enjoying Banglalink 4G internet with your 4G SIM. Dial *5000# to get the best internet offers.',
            'button' => 'Check Offer',
            'redirect_url' => '/banglalink-4g'
        ],
        "prepaid_non_eligible" => [
            'message' => 'Dear Customer, you are not eligible for this offer. You can replace your SIM to 4G SIM from any Banglalink SIM replacement point with replacement charge of BDT 200',
            'button' => 'Close',
            'redirect_url' => '/banglalink-4g'
        ],
        "non_4g_prepaid" => [
            'message' => 'Dear Customer, you are not using 4G sim. Dial*5000*40# or type FREE4G and send it to 2500 from your Banglalink number to know your 4G SIM replacement offer.',
            'button' => 'Close',
            'redirect_url' => '/banglalink-4g'
        ],
        "postpaid_non_eligible" => [
            'message' => 'Dear Customer please type "free4G" and send it to 5000 to know your eligibility or visit Banglalink Service Center',
            'button' => 'Close',
            'redirect_url' => '/banglalink-4g'
        ],
        "non_4g_postpaid" => [
            'message' => 'Dear Customer, please type "free4G" and send to 5000 to know your 4G SIM replacement offer or visit Banglalink Sales Point',
            'button' => 'Close',
            'redirect_url' => '/banglalink-4g'
        ]
    ];

    public function getSIMCardsUrl($customerId)
    {
        return self::CUSTOMER_ENDPOINT . "$customerId" . "/sim-cards";
    }

    public function getConnectionTypeUrl($customerId)
    {
        return self::CUSTOMER_ENDPOINT . "$customerId" . "/connection-types";
    }

    public function customerInfo($msisdn){
        return $this->blCustomerService->getCustomerInfoByNumber($msisdn);
    }

    public function connectionType($customerId)
    {
        $connectionType = $this->get($this->getConnectionTypeUrl($customerId));
        if ($connectionType['status_code'] == 200){
            return json_decode($connectionType['response'], true);
        } else {
            return $this->responseFormatter->sendErrorResponse([], 'Internal Server Error');
        }
    }

    public function uSIMEligibility($msisdn)
    {
        $separateNum = substr($msisdn, 0, 2);
        $msisdn = ($separateNum == "88") ? $msisdn : "88".$msisdn;

        $customerInfo = $this->customerInfo($msisdn);
        $customer_type = $customerInfo->getData()->data->connectionType;
        $customerId = $customerInfo->getData()->data->package->customerId;

        $response_data = $this->get($this->getSIMCardsUrl($customerId));

        if ($response_data['status_code'] == 200){
            $response = json_decode($response_data['response'], true)[0];
//            dd($response['simType']);
            if ($response['simType'] == "USIM")
            {
                $connectionType = $this->connectionType($customerId);
                if ($connectionType['status'] == "4G") {
                    $data = $this->eligibilityMessage['4g_usim'];
                    $data['customer_type'] = $customer_type;
                    // Use Test perpase
                    $data['status'] = $connectionType['status'];
                } else {
                    $data = ($customer_type == "PREPAID") ? $this->eligibilityMessage['non_4g_prepaid'] : $this->eligibilityMessage['non_4g_postpaid'];
                    // Use Test perpase
                    $data['status'] = $connectionType['status'];
                    $data['customer_type'] = $customer_type;
                }
            } else {
                $data = ($customer_type == "PREPAID") ? $this->eligibilityMessage['prepaid_non_eligible'] : $this->eligibilityMessage['postpaid_non_eligible'];
                // Use Test perpuse
                $data['customer_type'] = $customer_type;
            }
            return $this->responseFormatter->sendSuccessResponse($data, '4G USIM Eligibility Check');
        } else {
            return $this->responseFormatter->sendErrorResponse([], 'Internal Server Error');
        }
    }
}
