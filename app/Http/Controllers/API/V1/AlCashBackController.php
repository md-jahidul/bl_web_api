<?php

namespace App\Http\Controllers\API\V1;

use App\Services\AlCashBackService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AlCashBackController extends Controller
{
    /**
     * @var AlCashBackService
     */
    private $alCashBackService;

    /**
     * AlCashBackController constructor.
     * @param AlCashBackService $alCashBackService
     */
    public function __construct(AlCashBackService $alCashBackService) 
    {
        $this->alCashBackService = $alCashBackService;
    }

    public function getCashbackAmount(Request $request)
    {
        return $this->alCashBackService->getCashbackamount($request);
    }

}
