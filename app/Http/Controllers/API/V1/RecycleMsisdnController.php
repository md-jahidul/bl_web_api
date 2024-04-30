<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Services\Banglalink\RecycleMsisdnService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RecycleMsisdnController extends Controller
{
    /**
     * @var RecycleMsisdnService
     */
    protected $recycleMsisdnService;

    /**
     * RecycleMsisdnController constructor.
     * @param RecycleMsisdnService $recycleMsisdnService
     */
    public function __construct(RecycleMsisdnService $recycleMsisdnService)
    {
        $this->recycleMsisdnService = $recycleMsisdnService;
    }

    /**
     * Check Recycle MSISDN
     * 
     * @param int $msisdn
     * @return JsonResponse
     */
    public function recycleMsisdnCheck(Request $request)
    {
        return $this->recycleMsisdnService->checkRecycleMsisdn($request);
    }
    
}
