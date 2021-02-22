<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Services\AboutUsService;
use App\Services\CSRFTokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CSRFTokenController extends Controller
{
    /**
     * @var CSRFTokenService
     */
    private $CSRFTokenService;


    /**
     * CSRFTokenService constructor.
     * @param CSRFTokenService $CSRFTokenService
     */
    public function __construct(CSRFTokenService $CSRFTokenService)
    {
        $this->CSRFTokenService = $CSRFTokenService;
    }

    /**
     *
     */
    public function getToken()
    {
        return $this->CSRFTokenService->getCSRFToken();
    }
}
