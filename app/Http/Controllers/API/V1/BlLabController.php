<?php

namespace App\Http\Controllers\API\V1;


use App\Http\Controllers\Controller;
use App\Services\BlLabService;
use Illuminate\Http\Request;

class BlLabController extends Controller
{

    /**
     * @var BlLabService
     */
    private $blLabService;
    /**
     * BlLabController constructor.
     * @param BlLabService $blLabService
     */
    public function __construct(
        BlLabService $blLabService
    ) {
        $this->blLabService = $blLabService;
    }

    public function getBlLabPageData(Request $request)
    {
        // return $request->header('authorization');
        return $this->blLabService->getComponents($request);
    }


}
