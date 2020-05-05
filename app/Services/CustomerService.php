<?php

namespace App\Services;

use App\Enums\HttpStatusCode;
use App\Exceptions\IdpAuthException;
use App\Exceptions\TokenInvalidException;
use App\Exceptions\TokenNotFoundException;
use App\Exceptions\TooManyRequestException;
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
     * @return mixed
     * @throws IdpAuthException
     */
    public function getCustomerDetails(Request $request)
    {
        $bearerToken = ['token' => $request->header('authorization')];

        $response = IdpIntegrationService::tokenValidationRequest($bearerToken);

        if ($response['http_code'] != 200) {
            throw new AuthenticationException('Invalid authentication');
        }

        $idpData = json_decode($response['data']);
        if ($idpData->token_status != 'Valid') {
            throw new IdpAuthException('Invalid customer authentication token');
        }

        $customer = $this->customerRepository->getCustomerInfoByPhone($idpData->user->mobile);

        if (!$customer)
            throw new IdpAuthException('Customer not found');

        return $customer;
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
     * @throws TokenInvalidException
     * @throws TooManyRequestException
     * @throws TokenNotFoundException
     */
    public function getAuthenticateCustomer($request)
    {
        if (!$request->bearerToken()) {
            throw new TokenNotFoundException();
        }

        $bearerToken = ['token' => $request->header('authorization')];

        $response = IdpIntegrationService::tokenValidationRequest($bearerToken);

//        dd($response);

        if ($response['status_code'] == 429) {
            throw new TooManyRequestException();
        }

        $data = json_decode($response['response'], true);

        if ($data['token_status'] != 'Valid') {
            throw new TokenInvalidException();
        }

        return $this->customerRepository->getCustomerInfoByPhone($data['user']['mobile']);
    }


    /**
     * @param $phone
     * @return mixed
     */
    public function getCustomerInfoByPhone($phone)
    {
        return $this->customerRepository->getCustomerInfoByPhone($phone);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws TokenInvalidException
     */
    public function getCustomerBasicInfo(Request $request)
    {
        $bearerToken = ['token' => $request->header('authorization')];

        $response = IdpIntegrationService::tokenValidationRequest($bearerToken);

        $data = json_decode($response['response'], true);

        if ($data['token_status'] != 'Valid') {
            throw new TokenInvalidException();
        }

        if ($data['status'] != 'SUCCESS') {
            return $this->sendErrorResponse("IDp service Unavailable", [], 500);
        }

        $msisdn_key = 'mobile';
        $user = Customer::where('phone', $data['user'][$msisdn_key])->first();

        if (!$user) {
            throw new TokenInvalidException();
        }

        return $this->sendSuccessResponse(
            $this->prepareCustomerBasicInfo($user, $data['user']),
            'Customer Details Info'
        );
    }


    /**
     * @param $customer
     * @param $data
     * @return array
     */
    public function prepareCustomerBasicInfo($customer, $data)
    {
        return [
            'id' => $customer->id,
            'customer_account_id' => $customer->customer_account_id,
            'name' => isset($data['name']) ? $data['name'] : null,
            'msisdn_number' => $data['mobile'],
            'connection_type' => Customer::connectionType($customer),
            'email' => $data['email'],
            'birth_date' => isset($data['birth_date']) ? $data['birth_date'] : null,
            'enable_balance_transfer' => ($customer->balance_transfer_pin) ? true : false,
            'package' => Customer::package($customer),
            'is_password_set' => $data['is_password_set'] ? true : false
        ];
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws TokenInvalidException
     */
    public function getCustomerProfileImage(Request $request)
    {
        $bearerToken = ['token' => $request->header('authorization')];

        $response = IdpIntegrationService::tokenValidationRequest($bearerToken);

        $data = json_decode($response['response'], true);

        if ($data['token_status'] != 'Valid') {
            throw new TokenInvalidException();
        }


        if ($data['status'] != 'SUCCESS') {
            return $this->sendErrorResponse("IDp service Unavailable", [], 500);
        }

        $msisdn_key = 'mobile';

        $user = Customer::where('phone', $data['user'][$msisdn_key])->first();


        if (!$user) {
            throw new TokenInvalidException();
        }

        $response = IdpIntegrationService::getCustomerProfileImage($data['user'][$msisdn_key]);

        if ($response['status_code'] != 200) {
            return $this->sendErrorResponse('IDP Customer info Problem', [
                'message' => 'Something went wrong. try again later',
                'hint' => 'Getting HTTP error from IDP',
                'details' => []
            ], 400);
        }

        $data = json_decode($response['response'], true);

        $customer = $this->prepareCustomerProfileImage($user, $data['data']);

        return $this->sendSuccessResponse($customer, 'Customer Profile Image');
    }


    /**
     * @param $customer
     * @param $data
     * @return array
     */
    public function prepareCustomerProfileImage($customer, $data)
    {
        return [
            'name' => isset($data['name']) ? $data['name'] : null,
            'mobile' =>   isset($data['mobile']) ? $data['mobile'] : null,
            'profile_image' => isset($data['profile_image']) ? $data['profile_image'] : null
        ];
    }
}
