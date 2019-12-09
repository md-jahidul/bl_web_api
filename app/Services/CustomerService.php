<?php

namespace App\Services;

use App\Enums\HttpStatusCode;
use App\Http\Requests\DeviceTokenRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use App\Repositories\CustomerRepository;
use App\Services\Banglalink\CustomerPackageService;
use App\Traits\CrudTrait;
use http\Exception\RuntimeException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Class BannerService
 * @package App\Services
 */
class CustomerService extends ApiBaseService
{

    use CrudTrait;
    /**
     * @var CustomerRepository
     */
    protected $customerRepository;
    /**
     * @var CustomerPackageService
     */
    protected $CustomerPackageService;


    /**
     * CustomerService constructor.
     * @param CustomerRepository $customerRepository
     * @param CustomerPackageService $customerPackageService
     */
    public function __construct(CustomerRepository $customerRepository, CustomerPackageService $customerPackageService)
    {
        $this->customerRepository = $customerRepository;
        $this->CustomerPackageService = $customerPackageService;
        $this->setActionRepository($this->customerRepository);
    }


    /**
     *
     *
     * @param $request
     * @return JsonResponse
     */
    public function addNewCustomer($request)
    {

        try {
            $response = $this->customerRepository->create($request->all());
            return $this->sendSuccessResponse($response, 'New Customer');
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getCustomerDetails(Request $request)
    {
        $bearerToken = ['token' => $request->header('authorization')];

        $response = IdpIntegrationService::tokenValidationRequest($bearerToken);

        $idpData = json_decode($response['data']);

        if ($response['http_code'] != 200 || $idpData->token_status != 'Valid') {
            throw new AuthenticationException($idpData->token_status, $response['http_code']);
        }

        $customer = $this->getCustomerInfo($idpData->user->mobile);

        if (!$customer)
            throw new AuthenticationException('Customer not found');

        return $this->getCustomerInfo($idpData->user->mobile);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function updateCustomerDetails(Request $request)
    {
        // validate the token and get details info
        $bearerToken = ['token' => $request->header('authorization')];

        $response = IdpIntegrationService::tokenValidationRequest($bearerToken);

        $data = json_decode($response, true);

        if ($data['token_status'] != 'Valid') {
            return $this->sendErrorResponse("Token Invalid", [], HttpStatusCode::UNAUTHORIZED);
        }

        $msisdn_key = 'mobile';

        $customer = Customer::where('phone', $data['user'][$msisdn_key])->first();

        if (!$customer) {
            return $this->sendErrorResponse("Token Invalid", [], HttpStatusCode::UNAUTHORIZED);
        }

        // Customer Update

        // if any profile_image added

        $path = null;

        if ($request->hasFile('profile_image')) {
            try {
                $file = $request->file('profile_image');
                $path = $file->storeAs(
                    'uploads/profile-images',
                    md5(strtotime(now())) . '.' . $file->getClientOriginalExtension(),
                    'public'
                );
            } catch (\Exception $e) {
                return $this->sendErrorResponse($e->getMessage(), [], 500);
            }
        }

        $update_data = $request->only([
            'name', 'email', 'birth_date'
        ]);

        if ($path) {
            $update_data['profile_image'] = $path;
        }

        if (!$request->has('birth_date')) {
            $update_data['birth_date'] = null;
        }

        $customer->update($update_data);


        return $this->sendSuccessResponse(new CustomerResource($customer), 'Customer Details Updated');
    }


    /**
     * Authenticate customer Info
     *
     * @param $request
     * @return mixed
     */
    public function getAuthenticateCustomer($request)
    {
        $bearerToken = ['token' => $request->header('authorization')];


        $response = IdpIntegrationService::tokenValidationRequest($bearerToken);

        $data = json_decode($response, true);


        if ($data['token_status'] != 'Valid') {
            return $this->sendErrorResponse("Token is Invalid", [], HttpStatusCode::UNAUTHORIZED);
        }

        $customer = $this->customerRepository->getCustomerInfoByPhone($data['user']['mobile']);

        return $customer;
    }


    public function getCustomerInfoByPhone($phone)
    {
        return $this->customerRepository->getCustomerInfoByPhone($phone);
    }
}
