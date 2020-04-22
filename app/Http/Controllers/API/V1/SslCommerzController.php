<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use App\Services\SslCommerzService;
use App\Http\Controllers\Controller;
use App\Http\Requests\RechargeRequest;
use App\Http\Requests\InitiatePaymentRequest;


/**
 * Class SslCommerzController
 * @package App\Http\Controllers\API\V1
 */
class SslCommerzController extends Controller
{
    /**
     * @var SslCommerzService
     */
    protected $sslCommerzService;

    /**
     * SslCommerzController constructor.
     * @param SslCommerzService $sslCommerzService
     */
    public function __construct(SslCommerzService $sslCommerzService)
    {
        $this->sslCommerzService = $sslCommerzService;
    }

    /**
     * @param RechargeRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function rechargeViaSsl(RechargeRequest $request)
    {
        return $this->sslCommerzService->rechargeViaSsl($request);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function success(Request $request)
    {
        return $this->sslCommerzService->success($request);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function failure(Request $request)
    {
        return $this->sslCommerzService->failure($request);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancel(Request $request)
    {
        return $this->sslCommerzService->cancel($request);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRequestDetails(Request $request)
    {
        return $this->sslCommerzService->getRequestDetails($request);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function paymentView()
    {
        return view('payment');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function paymentRequestSubmit(Request $request)
    {
        return $this->sslCommerzService->paymentRequestSubmit($request);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function initiatePayment(InitiatePaymentRequest $request)
    {
        return $this->sslCommerzService->initiatePaymentRequest($request);
    }

}
