<?php

namespace App\Services;

use App\Enums\HttpStatusCode;
use App\Http\Resources\SwitchAccountResource;
use App\Repositories\LinkAccountRepository;
use App\Services\Banglalink\BanglalinkCustomerService;

class SwitchAccountService extends ApiBaseService
{

    /**
     * @var LinkAccountRepository
     */
    protected $linkAccountRepository;


    /**
     * @var CustomerService
     */
    protected $customerService;


    /**
     * @var BanglalinkCustomerService
     */
    protected $blCustomerService;


    /**
     * SwitchAccountService constructor.
     * @param LinkAccountRepository $linkAccountRepository
     * @param BanglalinkCustomerService $blCustomerService
     */
    public function __construct(LinkAccountRepository $linkAccountRepository, CustomerService $customerService,
                                BanglalinkCustomerService $blCustomerService
                                )
    {
        $this->linkAccountRepository = $linkAccountRepository;
        $this->customerService = $customerService;
        $this->blCustomerService = $blCustomerService;
    }


    /**
     * Link Account Info
     *
     * @param $request
     * @return mixed
     */
    public function getLinkAccountInfo($request)
    {
        try {
            $customer = $this->customerService->getAuthenticateCustomer($request);

            if (!$customer) {
                return $this->sendErrorResponse("Token Invalid", [], HttpStatusCode::UNAUTHORIZED);
            }

            $main_account['id'] = $customer['id'];
            $main_account['name'] = $customer['name'];
            $main_account['mobile'] = $customer['phone'];

            $linkAccount = $this->linkAccountRepository->getLinkAccountInfo($customer->id);

            $data['main_account'] = $main_account;
            $data['link_account'] = $linkAccount;

           // $formatted_data = SwitchAccountResource::collection($data);

            return $this->sendSuccessResponse($data, 'Link Account Info', [], HttpStatusCode::SUCCESS);
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage(), [], HttpStatusCode::INTERNAL_ERROR);
        }
    }


    /**
     * Add link account
     *
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addLinkAccount($request)
    {
        $data = $request->all();

        $mobile = "88" . $data['mobile'];

        $customer = $this->customerService->getAuthenticateCustomer($request);

        if (!$customer) {
            return $this->sendErrorResponse("Token Invalid", [], HttpStatusCode::UNAUTHORIZED);
        }

        $data['customer_id'] = $customer->id;

        $customer_info = $this->blCustomerService->getCustomerInfoByNumber($mobile);

        if ($customer_info->getData()->status == "FAIL") {
            return $this->sendErrorResponse("User not found", [], HttpStatusCode::NOT_FOUND);
        }

        $customer_account_id = $customer_info->getData()->data->package->customerId;

        $data['customer_account_id'] = $customer_account_id;


        try {
            $check = $this->linkAccountRepository->checkExistOrNOt($data);

            if ($check) {
                $message = 'This Link Account is already exist';

                return $this->sendErrorResponse($message, [], HttpStatusCode::VALIDATION_ERROR);
            }
            $this->linkAccountRepository->create($data);

            $message = 'Link Account Successfully added';

            return $this->sendSuccessResponse([], $message, [], HttpStatusCode::SUCCESS);
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage(), [], HttpStatusCode::INTERNAL_ERROR);
        }
    }



    /**
     * Add link account
     *
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function arrangeLinkAccount($request)
    {
        $data = $request->all();
        $customer = $this->customerService->getAuthenticateCustomer($request);

        if (!$customer) {
            return $this->sendErrorResponse("Token Invalid", [], HttpStatusCode::UNAUTHORIZED);
        }

        try {
            $linkAccount = $this->linkAccountRepository->arrangeLinkAccount($data);
            if ($linkAccount) {
                $message = 'Link Account Successfully arranged';
                return $this->sendSuccessResponse([], $message, [], HttpStatusCode::SUCCESS);
            } else {
                $message = 'Account is not exist';
                return $this->sendErrorResponse($message, [], HttpStatusCode::NOT_FOUND);
            }
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage(), [], HttpStatusCode::INTERNAL_ERROR);
        }
    }



    /**
     * Add link account
     *
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeLinkAccount($request)
    {
        $data = $request->all();
        $customer = $this->customerService->getAuthenticateCustomer($request);

        if (!$customer) {
            return $this->sendErrorResponse("Token Invalid", [], HttpStatusCode::UNAUTHORIZED);
        }

        try {
            $linkAccount = $this->linkAccountRepository->removeLinkAccount($data);
            if ($linkAccount) {
                $message = 'Link Account Successfully removed';
                return $this->sendSuccessResponse([], $message, [], HttpStatusCode::SUCCESS);
            } else {
                $message = 'Account is not exist';
                return $this->sendErrorResponse($message, [], HttpStatusCode::NOT_FOUND);
            }
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage(), [], HttpStatusCode::INTERNAL_ERROR);
        }
    }
}
