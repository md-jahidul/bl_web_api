<?php

namespace App\Http\Controllers\API\V1;

use App\Enums\HttpStatusCode;
use App\Exceptions\IdpAuthException;
use App\Http\Controllers\Controller;
use App\Services\AboutUsService;
use App\Services\AppAndService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


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

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws IdpAuthException
     */
    public function bookmarkSaveOrDelete(Request $request)
    {
        $validator = Validator::make($request->all(), ['product_id' => 'required', 'operation_type' => 'required']);
        if ($validator->fails()) {
            return response()->json($validator->messages(), HttpStatusCode::VALIDATION_ERROR);
        }
        return $this->appAndService->customerAppServiceBookmark($request);
    }

    public function appServiceLike($productId)
    {
       return $this->appAndService->like($productId);
    }
}
