<?php

namespace App\Http\Controllers\API\V1\BlLab;

use App\Exceptions\IdpAuthException;
use App\Http\Controllers\Controller;
use App\Http\Requests\BlLabRegisterRequest;
use App\Http\Requests\BlLabVerifyOTPRequest;
use App\Services\AboutUsService;
use App\Services\BlLabs\BlLabApplicationContentService;
use App\Services\BlLabs\BlLabsAuthenticationService;
use App\Services\BlLabs\BlLabsIdeaSubmitService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class BlLabApplicationContentController extends Controller
{
    /**
     * @var BlLabApplicationContentService
     */
    private $applicationContentService;

    /**
     * BlLabApplicationContentController constructor.
     * @param BlLabApplicationContentService $applicationContentService
     */
    public function __construct(BlLabApplicationContentService $applicationContentService)
    {
        $this->applicationContentService = $applicationContentService;
    }

    public function getIndustry()
    {
        return $this->applicationContentService->industry();
    }

    public function getProgram()
    {
        return $this->applicationContentService->program();
    }

    public function getProfession()
    {
        return $this->applicationContentService->profession();
    }

    public function getInstituteOrOrg()
    {
        return $this->applicationContentService->instituteOrOrg();
    }

    public function getEducation()
    {
        return $this->applicationContentService->education();
    }

    public function getStartupStage()
    {
        return $this->applicationContentService->startupStage();
    }

    public function getBanner()
    {
        return $this->applicationContentService->banner();
    }
}
