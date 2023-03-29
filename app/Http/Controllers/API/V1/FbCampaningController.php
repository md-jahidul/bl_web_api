<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\FbCampaningService;

class FbCampaningController extends Controller
{
    /**
     * @var fbCampaningService
     */
    private $fbCampaningService;

    /**
     * AboutUsController constructor.
     * @param FaqService $fbCampaningService
     */
    public function __construct(FbCampaningService $fbCampaningService)
    {
        $this->fbCampaningService = $fbCampaningService;
    }
    /**
     * @param Re $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        return $this->fbCampaningService->storeData($request->all());
    }
}
