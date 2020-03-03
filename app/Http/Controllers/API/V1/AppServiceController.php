<?php

namespace App\Http\Controllers\API\V1;

use App\Exceptions\IdpAuthException;
use App\Http\Controllers\Controller;
use App\Services\AboutUsService;
use App\Services\AppAndService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class AppServiceController extends Controller
{

    /**
     * @var AboutUsService
     */
    protected $appAndService;


    /**
     * AboutUsController constructor.
     * @param AppAndService $appAndService
     */
    public function __construct(AppAndService $appAndService)
    {
        $this->appAndService = $appAndService;
    }

    /**
     * @return JsonResponse
     */
    public function appServiceAllComponent()
    {
        return $this->appAndService->appServiceData();
    }

    public function packageList($provider)
    {

       return $this->appAndService->packageList($provider);
    }
}
