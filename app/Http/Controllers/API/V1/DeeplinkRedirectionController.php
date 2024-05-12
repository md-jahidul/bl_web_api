<?php

namespace App\Http\Controllers\API\V1;

use App\Exceptions\IdpAuthException;
use App\Http\Controllers\Controller;
use App\Services\AboutUsService;
use App\Services\DeeplinkRedirectionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class DeeplinkRedirectionController extends Controller
{
    /**
     * @var DeeplinkRedirectionService
     */
    private $redirectionService;


    /**
     * DeeplinkRedirectionController constructor.
     * @param DeeplinkRedirectionService $redirectionService
     */
    public function __construct(DeeplinkRedirectionService $redirectionService)
    {
        $this->redirectionService = $redirectionService;
    }

    /**
     * @return JsonResponse
     */
    public function getRedirectionLink(Request $request)
    {
        return $this->redirectionService->getRedirectionLink($request->all());
    }
}
