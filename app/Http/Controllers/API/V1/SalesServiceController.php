<?php

namespace App\Http\Controllers\API\V1;

use App\Services\SalesAndServicesService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreLocatorRequest;

class SalesServiceController extends Controller
{

    /**
     * @var $salesAndServicesService
     */
    private $salesAndServicesService;

    /**
     * SalesServiceController constructor.
     * @param SalesAndServicesService $salesAndServicesService
     */
    public function __construct(SalesAndServicesService $salesAndServicesService)
    {
        $this->salesAndServicesService = $salesAndServicesService;
    }

    /**
     * @return mixed
     */
    public function salesServiceSearchResutls(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'district' => 'required|string',
            'thana' => 'nullable|string',
        ]);
        if ($validator->fails()) {
            return response()->json((['status' => 'FAIL', 'status_code' => HttpStatusCode::VALIDATION_ERROR, 'message' =>  $validator->messages()->first(), 'errors' => [] ]), HttpStatusCode::VALIDATION_ERROR);
        }

        return $this->salesAndServicesService->getSearchResults($request->all());
    }

    /**
     * [salesServiceGetDistricts description]
     * @return [type] [description]
     */
    public function salesServiceGetDistricts()
    {
        return $this->salesAndServicesService->getDistricts();
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function salesServiceThanaByDistricts(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'district' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json((['status' => 'FAIL', 'status_code' => HttpStatusCode::VALIDATION_ERROR, 'message' =>  $validator->messages()->first(), 'errors' => [] ]), HttpStatusCode::VALIDATION_ERROR);
        }

        return $this->salesAndServicesService->getThanaByDistricts($request->all());
    }


     /**
     *
     * @param StoreLocatorRequest $request
     * @return JsonResponse
     */
    public function getNearestStoreLocations(StoreLocatorRequest $request)
    {
        return $this->salesAndServicesService->getNearestLocations($request);
    }

}
