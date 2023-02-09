<?php

namespace App\Http\Controllers\API\V1;

use App\Exceptions\TokenInvalidException;
use App\Http\Controllers\Controller;
use App\Services\Banglalink\BalanceService;
use App\Services\Banglalink\ProductLoanService;
use App\Services\CurrentBalanceService;
use App\Services\IdpIntegrationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class CurrentBalanceController
 * @package App\Http\Controllers\API\V1
 */
class CurrentBalanceController extends Controller
{
    /**
     * @var CurrentBalanceService
     */
    protected $balanceService;
    /**
     * @var IdpIntegrationService
     */
    protected $idpService;

    protected $balanceTransferService;
    /**
     * @var ProductLoanService
     */
    private $productLoanService;


    /**
     * CurrentBalanceController constructor.
     * @param BalanceService $balanceService
     * @param IdpIntegrationService $idpIntegrationService
     */
    public function __construct(
        BalanceService $balanceService,
        IdpIntegrationService $idpIntegrationService,
        ProductLoanService $productLoanService
    ) {
        $this->balanceService = $balanceService;
        $this->idpService = $idpIntegrationService;
        $this->productLoanService = $productLoanService;
        //$this->middleware('idp.verify');
    }

    /**
     * Retrieve current balance
     *
     * @param Request $request
     * @return mixed|string
     */
    public function getBalanceSummary(Request $request)
    {
        return $this->balanceService->getBalanceSummary($request);
    }


    /**
     * Get Balance Details
     *
     * @param $type
     * @param Request $request
     * @return JsonResponse
     */
    public function getBalanceDetails($type, Request $request)
    {
        return $this->balanceService->getBalanceDetails($type, $request);
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


    public function customerLoanCheck(Request $request)
    {
        return $this->productLoanService->getLoanAmount($request);
    }
}
