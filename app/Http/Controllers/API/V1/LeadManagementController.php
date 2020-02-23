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

//    /**
//     * @param LeadRequestCheck $request
//     */
    public function leadRequestData(Request $request)
    {
        $rules = [
            'name' => 'required',
            'company_name' => 'required',
            'mobile' => 'required',
            'email' => 'required|email|unique:lead_requests',
            'district' => 'required',
            'thana'=> 'required',
            'address' => 'required',
            'quantity' => 'required',
            'package' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json((['status' => 'FAIL', 'status_code' => HttpStatusCode::VALIDATION_ERROR, 'message' =>  $validator->messages()->first() ]), HttpStatusCode::VALIDATION_ERROR);
        }

        return $this->leadRequestService->saveRequest($request->all());
//        Session::flash('message', $response->getContent());
    }

}
