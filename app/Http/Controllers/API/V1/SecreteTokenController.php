<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Services\AboutUsService;
use App\Services\CSRFTokenService;
use App\Services\SecreteTokenService;
use App\Services\TokenGeneratorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SecreteTokenController extends Controller
{
    /**
     * @var SecreteTokenService
     */
    private $secreteTokenService;


    /**
     * SecreteTokenService constructor.
     * @param SecreteTokenService $secreteTokenService
     */
    public function __construct(SecreteTokenService $secreteTokenService)
    {
        $this->secreteTokenService = $secreteTokenService;
    }

    /**
     *
     */
    public function getToken()
    {
        return $this->secreteTokenService->generateToken();
    }
}
