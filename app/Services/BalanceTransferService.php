<?php

namespace App\Services;

use App\Enums\HttpStatusCode;
use App\Exceptions\BalanceTransferFailedException;
use App\Exceptions\BLServiceException;
use App\Exceptions\CurlRequestException;
use App\Exceptions\OldPinInvalidException;
use App\Exceptions\PinAlreadySetException;
use App\Exceptions\PinInvalidException;
use App\Exceptions\PinNotSetException;
use App\Exceptions\TokenInvalidException;
use App\Exceptions\TokenNotFoundException;
use App\Exceptions\TooManyRequestException;
use App\Repositories\TermsAndConditionRepository;
use App\Services\Banglalink\BaseService;
use App\Traits\CrudTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * Class CurrentBalanceService
 * @package App\Services
 */
class BalanceTransferService extends BaseService
{
    use CrudTrait;

    /**as
     * @var CustomerService
     */
    private $customerService;
    /**
     * @var TermsAndConditionRepository
     */
    private $termsAndConditionRepository;
    /**
     * @var ApiBaseService
     */
    private $apiBaseService;

    protected const GENERATE_PIN_ENDPOINT = "/provisioning/transfer/generate-pin";
    protected const BALANCE_TRANSFER_ENDPOINT = "/provisioning/transfer/balance-transfer";

    /**
     * CustomerService constructor.
     * @param CustomerService $customerService
     */
    public function __construct(
        ApiBaseService $apiBaseService,
        CustomerService $customerService,
        TermsAndConditionRepository $termsAndConditionRepository
    ) {
        $this->apiBaseService = $apiBaseService;
        $this->customerService = $customerService;
        $this->termsAndConditionRepository = $termsAndConditionRepository;
    }

    private function generatePin($customer_id)
    {
        $end_point = self::GENERATE_PIN_ENDPOINT . "?subscriptionId=" . $customer_id;

        $result = $this->get($end_point);
        $response = json_decode($result['response'], true);

        if ($result['status_code'] == 200) {
            if ($response['messgeCode'] == "200" &&  $response['message'] == "OK") {
                return $response ['data']['data']['attributes']['pin'];
            }
        }
        throw new BLServiceException($response);
    }

    private function validateCustomerPin($customer, $pin)
    {
        $hashed_password  = $customer->balance_transfer_pin;
        return Hash::check($pin, $hashed_password);
    }

    /**
     * Transfer Balance
     *
     * @param $request
     * @return JsonResponse
     * @throws BLServiceException
     * @throws CurlRequestException
     * @throws TokenInvalidException
     * @throws PinInvalidException
     * @throws BalanceTransferFailedException
     */
    public function transferBalance($request)
    {
        $customer = $this->customerService->getAuthenticateCustomer($request);

        $param = [
            'amount' => $request->amount,
            'transactionPIN' => $this->generatePin($customer->customer_account_id),
            'sendingMsisdn' => $customer->msisdn,
            'receivingMsisdn' => '88' . $request->transfer_to
        ];

        $result = $this->post(self::BALANCE_TRANSFER_ENDPOINT, $param);

        $response = json_decode($result['response'], true);
        $metaErrorCode = $response['data']['errors'][0]['meta']['error_code'] ?? null;

        if ($result['status_code'] == 200) {
            if ($response['messgeCode'] == 200 &&  $response['message'] == "OK") {
                return $this->apiBaseService->sendSuccessResponse(
                    [],
                    "Balance transfer request submitted successfully.",
                    [],
                    HttpStatusCode::SUCCESS
                );
            }

            if($metaErrorCode) {
                $code = (int)$metaErrorCode;
                $message = $response['data']['errors'][0]['detail'];

                if ($code === 1140) {
                    $response['data']['errors'][0]['detail'] = "Balance transfer failed . Postpaid number is not applicable for this service. ";
                }
                else if($code === 1310){
                    $response['data']['errors'][0]['detail'] = "Balance transfer failed . Please try after 30 minutes from last balance transfer time.";
                }
            }
            /*   return $this->apiBaseService->sendErrorResponse(
                   $response ['data']['errors'][0]['detail'],
                   [],
                   400
            );*/

            throw new BalanceTransferFailedException($response);
        }

        throw new CurlRequestException($result);
    }

    public function checkPin($request)
    {
        $customer = $this->customerService->getAuthenticateCustomer($request);

        if (!$customer->balance_transfer_pin) {
            throw new PinNotSetException();
        }

        if (!$this->validateCustomerPin($customer, $request->pin)) {
            throw new PinInvalidException();
        }

        return $this->apiBaseService->sendSuccessResponse([], 'This is a valid pin');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws PinAlreadySetException
     * @throws TokenInvalidException
     * @throws TokenNotFoundException
     * @throws TooManyRequestException
     */
    public function setTransferPin(Request $request)
    {
        $user = $this->customerService->getAuthenticateCustomer($request);

        if (!$user) {
            return $this->apiBaseService->sendErrorResponse("User not found", [], HttpStatusCode::UNAUTHORIZED);
        }

        if ($user->balance_transfer_pin) {
            throw new PinAlreadySetException();
        }

        $user->balance_transfer_pin = Hash::make($request->pin);
        $user->save();

        return $this->apiBaseService->sendSuccessResponse([], 'Balance Transfer Pin is Saved Successfully');
    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @throws OldPinInvalidException
     * @throws PinNotSetException
     * @throws TokenInvalidException
     * @throws TokenNotFoundException
     * @throws TooManyRequestException
     */
    public function changeTransferPin(Request $request)
    {
        $user = $this->customerService->getAuthenticateCustomer($request);

        if (!$user) {
            throw new TokenInvalidException();
        }

        if (!$user->balance_transfer_pin) {
            throw new PinNotSetException();
        }

        $hashed_password = $user->balance_transfer_pin;

        if (!Hash::check($request->old_pin, $hashed_password)) {
            throw new OldPinInvalidException();
        }

        $user->balance_transfer_pin = Hash::make($request->new_pin);
        $user->save();

        return $this->apiBaseService->sendSuccessResponse([], 'Balance Transfer Pin is changed Successfully');
    }

    public function resetTransferPin(Request $request)
    {
        $customer = $this->customerService->getAuthenticateCustomer($request);

        if (!$customer) {
            throw new TokenInvalidException();
        }

        $customer->balance_transfer_pin = Hash::make($request->new_pin);

        $customer->save();

        return $this->apiBaseService->sendSuccessResponse([], 'Balance Transfer Pin is reset Successfully');
    }

    public function termAndCondition()
    {
        $data = $this->termsAndConditionRepository->findOneByProperties(['platform' => 'website', 'feature_name' => 'balance_transfer'],
            ['terms_conditions as terms_conditions_en', 'terms_conditions_bn']
        );

        return $this->apiBaseService->sendSuccessResponse($data, 'Balance transfer terms and condition');
    }
}
