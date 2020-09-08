<?php

namespace App\Http\Controllers\API\V1;

use App\Exceptions\IdpAuthException;
use App\Http\Controllers\Controller;
use App\Services\AboutUsService;
use App\Services\CustomerFeedbackService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class CustomerFeedbackController extends Controller
{
    /**
     * @var CustomerFeedbackService
     */
    private $customerFeedbackService;


    /**
     * AboutUsController constructor.
     * @param CustomerFeedbackService $customerFeedbackService
     */
    public function __construct(CustomerFeedbackService $customerFeedbackService)
    {
        $this->customerFeedbackService = $customerFeedbackService;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws IdpAuthException
     */
    public function getQuestionAns()
    {
        return $this->customerFeedbackService->getQuestionAns();
    }


}
