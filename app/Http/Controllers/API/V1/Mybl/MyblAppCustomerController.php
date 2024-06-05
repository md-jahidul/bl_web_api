<?php

namespace App\Http\Controllers\API\V1\Mybl;

use App\Exceptions\IdpAuthException;
use App\Http\Controllers\Controller;
use App\Services\AboutUsService;
use App\Services\Mybl\MyblAppCustomerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class MyblAppCustomerController extends Controller
{
    /**
     * @var MyblAppCustomerService
     */
    private $myblAppCustomerService;

    /**
     * MyblAppCustomerController constructor.
     * @param MyblAppCustomerService $myblAppCustomerService
     */
    public function __construct(MyblAppCustomerService $myblAppCustomerService)
    {
        $this->myblAppCustomerService = $myblAppCustomerService;
    }

    /**
     * @return JsonResponse
     */
    public function sendOtp(Request $request)
    {
        return $this->myblAppCustomerService->otpRequest($request);
    }

    /**
     * @return JsonResponse
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        return $this->myblAppCustomerService->otpVerify($request);
    }

    /**
     * @return JsonResponse
     */
    public function feedbackQuestions(): JsonResponse
    {
        return $this->myblAppCustomerService->feedbackQuestion();
    }
    /**
     * @return JsonResponse
     */
    public function deleteTnc(): JsonResponse
    {
        return $this->myblAppCustomerService->deleteTnc();
    }

    /**
     * @return JsonResponse
     */
    public function customerAccountDelete(Request $request): JsonResponse
    {
        return $this->myblAppCustomerService->deleteAccount($request);
    }
}
