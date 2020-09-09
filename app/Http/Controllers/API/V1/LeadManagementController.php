<?php

namespace App\Http\Controllers\API\V1;

use App\Enums\HttpStatusCode;
use App\Http\Controllers\Controller;
use App\Http\Requests\LeadRequest;
use App\Http\Requests\LeadRequestCheck;
use App\Services\Banglalink\LeadRequestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class LeadManagementController extends Controller
{
    /**
     * @var $leadRequestService
     */
    protected $leadRequestService;


    /**
     * LeadManagementController constructor.
     * @param LeadRequestService $leadRequestService
     */
    public function __construct(LeadRequestService $leadRequestService)
    {
        $this->leadRequestService = $leadRequestService;
    }

    /**
     * @param LeadRequest $request
     * @return string
     */
    public function leadRequestData(Request $request)
    {
        return $this->leadRequestService->saveRequest($request->all());
    }

}
