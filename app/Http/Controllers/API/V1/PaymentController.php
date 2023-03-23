<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Services\Payment\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * @var PaymentService
     */
    private $paymentService;


    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * @return JsonResponse|mixed
     */
    public function paymentGateways()
    {
        return $this->paymentService->paymentGateways();
    }

    /**
     * @param Request $request
     * @return JsonResponse|mixed
     */
    public function ownRgwInitiatePayment(Request $request)
    {
        return $this->paymentService->ownRgwPayment($request->all());
    }
}
