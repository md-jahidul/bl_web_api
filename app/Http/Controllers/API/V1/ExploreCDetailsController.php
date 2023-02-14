<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Services\ExploreCDetailsService;
use Illuminate\Http\Request;

class ExploreCDetailsController extends Controller
{
    protected $exploreCDetailsService;

    public function __construct(ExploreCDetailsService $exploreCDetailsService)
    {
        $this->exploreCDetailsService = $exploreCDetailsService;
    }

    /**
     * @return JsonResponse|mixed
     */
    public function getExploreCDeatils($explore_c_page_slug)
    {
        return $this->exploreCDetailsService->getExploreCDetailsComponent($explore_c_page_slug);
    }
}
