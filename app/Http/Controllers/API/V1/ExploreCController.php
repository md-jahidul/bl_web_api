<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Services\ExploreCService;
use Illuminate\Http\Request;

class ExploreCController extends Controller
{
    protected $exploreCService;

    public function __construct(ExploreCService $exploreCService)
    {
        $this->exploreCService = $exploreCService;
    }

    /**
     * @return JsonResponse|mixed
     */
    public function getExploreC()
    {
        return $this->exploreCService->getExploreC();
    }
    
}
