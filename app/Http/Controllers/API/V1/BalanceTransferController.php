<?php

namespace App\Http\Controllers\API\V1;

use App\Enums\HttpStatusCode;
use App\Exceptions\BLServiceException;
use App\Exceptions\CurlRequestException;
use App\Exceptions\OldPinInvalidException;
use App\Exceptions\PinAlreadySetException;
use App\Exceptions\PinInvalidException;
use App\Exceptions\PinNotSetException;
use App\Exceptions\TokenInvalidException;
use App\Http\Requests\ChangeBalanceTransferPinRequest;
use App\Http\Requests\CustomerRequest;
use App\Http\Requests\DeviceTokenRequest;
use App\Http\Requests\ResetBalanceTransferPinRequest;
use App\Http\Requests\SetBalanceTransferPinRequest;
use App\Http\Requests\SetPasswordRequest;
use App\Http\Requests\TransferBalanceRequest;
use App\Http\Requests\UpdateCustomerDetailsRequest;
use App\Services\BalanceTransferService;
use App\Services\Banglalink\BanglalinkCustomerService;
use App\Services\Banglalink\BaringService;
use App\Services\Banglalink\SimService;
use App\Services\CustomerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BalanceTransferController extends Controller
{
    /**
     * @var BalanceTransferService
     */
    private $balanceTransferService;


    /**
     * BalanceTransferController constructor.
     * @param BalanceTransferService $balanceTransferService
     */
    public function __construct(
        BalanceTransferService $balanceTransferService
    ) {
        $this->balanceTransferService = $balanceTransferService;
    }

    /**
     * Transfer Balance
     *
     * @param TransferBalanceRequest $request
     * @return JsonResponse
     * @throws BLServiceException
     * @throws CurlRequestException
     * @throws PinInvalidException
     * @throws TokenInvalidException
     */
    public function transferBalance(TransferBalanceRequest $request)
    {
        return $this->balanceTransferService->transferBalance($request);
    }

    public function pinVerify(Request $request)
    {
        return $this->balanceTransferService->checkPin($request);
    }

    /**
     * @param SetBalanceTransferPinRequest $request
     * @return JsonResponse
     * @throws PinAlreadySetException
     */
    public function generateCustomerPin(SetBalanceTransferPinRequest $request): JsonResponse
    {
        return $this->balanceTransferService->setTransferPin($request);
    }

    /**
     * @param ChangeBalanceTransferPinRequest $request`
     * @return JsonResponse
     * @throws OldPinInvalidException
     * @throws PinNotSetException
     * @throws \App\Exceptions\TokenInvalidException
     */
    public function changeCustomerPin(ChangeBalanceTransferPinRequest $request)
    {
        return $this->balanceTransferService->changeTransferPin($request);
    }

    public function resetCustomerPin(ResetBalanceTransferPinRequest $request)
    {
        return $this->balanceTransferService->resetTransferPin($request);
    }

    public function balanceTransferConditions()
    {
        return $this->balanceTransferService->termAndCondition();
    }
}
