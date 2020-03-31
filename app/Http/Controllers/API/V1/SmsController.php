<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\SmsRequest;
use App\Services\Banglalink\SmsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class SmsController extends Controller
{

    /**
     * @var SmsService
     */
    protected $smsService;


    /**
     * AboutUsController constructor.
     * @param SmsService $smsService
     */
    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * @param SmsRequest $request
     * @return JsonResponse
     */
    public function sendSms(SmsRequest $request)
    {
        return $this->smsService->sendSms($request);
    }



}
