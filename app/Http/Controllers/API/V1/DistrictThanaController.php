<?php

namespace App\Http\Controllers\API\V1;

use App\Exceptions\IdpAuthException;
use App\Http\Controllers\Controller;
use App\Services\AboutUsService;
use App\Services\DistrictThanaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class DistrictThanaController extends Controller
{
    /**
     * @var $districtThanaService
     */
    protected $districtThanaService;

    public function __construct(DistrictThanaService $districtThanaService)
    {
        $this->districtThanaService = $districtThanaService;
    }

    /**
     * @return mixed
     */
    public function district()
    {
       return $this->districtThanaService->district();
    }

    /**
     * @param $districtId
     * @return JsonResponse|mixed
     */
    public function thana($districtId)
    {
        return $this->districtThanaService->thana($districtId);
    }
}
