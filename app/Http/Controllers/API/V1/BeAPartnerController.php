<?php

namespace App\Http\Controllers\API\V1;

use App\Exceptions\IdpAuthException;
use App\Http\Controllers\Controller;
use App\Services\AboutUsService;
use App\Services\BeAPartnerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class BeAPartnerController extends Controller
{
    /**
     * @var BeAPartnerService
     */
    private $beAPartnerService;

    /**
     * AboutUsController constructor.
     * @param BeAPartnerService $beAPartnerService
     */
    public function __construct(BeAPartnerService $beAPartnerService)
    {
        $this->beAPartnerService = $beAPartnerService;
    }

    /**
     * @return JsonResponse|mixed
     */
    public function getBeAPartner()
    {
        return $this->beAPartnerService->beAPartnerData();
    }
}
