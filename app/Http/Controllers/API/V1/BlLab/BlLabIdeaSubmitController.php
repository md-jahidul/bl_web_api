<?php

namespace App\Http\Controllers\API\V1\BlLab;

use App\Exceptions\IdpAuthException;
use App\Http\Controllers\Controller;
use App\Http\Requests\BlLabRegisterRequest;
use App\Http\Requests\BlLabVerifyOTPRequest;
use App\Services\AboutUsService;
use App\Services\BlLabs\BlLabsAuthenticationService;
use App\Services\BlLabs\BlLabsIdeaSubmitService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class BlLabIdeaSubmitController extends Controller
{
    /**
     * @var BlLabsIdeaSubmitService
     */
    private $labsIdeaSubmitService;

    /**
     * BlLabIdeaSubmitController constructor.
     * @param BlLabsIdeaSubmitService $labsIdeaSubmitService
     */
    public function __construct(BlLabsIdeaSubmitService $labsIdeaSubmitService)
    {
        $this->labsIdeaSubmitService = $labsIdeaSubmitService;
    }

    public function ideaSubmit(Request $request)
    {
        return $this->labsIdeaSubmitService->storeIdea($request);
    }

    public function getIdeaSubmittedData(Request $request)
    {
        return $this->labsIdeaSubmitService->ideaSubmittedData($request);
    }

    public function applicationStage(Request $request)
    {
        return $this->labsIdeaSubmitService->getApplicationCurrentStage($request);
    }

    public function applicationList()
    {
        return $this->labsIdeaSubmitService->applicationList();
    }

    public function applicationDownload($applicationId)
    {
        return $this->labsIdeaSubmitService->generatePDF($applicationId);
    }
}
