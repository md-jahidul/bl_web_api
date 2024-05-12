<?php

namespace App\Services;

use App\Enums\HttpStatusCode;
use App\Repositories\DeeplinkRedirectionRepository;
use Illuminate\Http\JsonResponse;

class DeeplinkRedirectionService extends ApiBaseService
{
    /**
     * @var DeeplinkRedirectionRepository
     */
    private $redirectionRepository;


    /**
     * DeeplinkRedirectionService constructor.
     * @param DeeplinkRedirectionRepository $redirectionRepository
     */
    public function __construct(
        DeeplinkRedirectionRepository $redirectionRepository
    ) {
        $this->redirectionRepository = $redirectionRepository;
    }

    /**
     * @return JsonResponse
     */
    public function getRedirectionLink($data)
    {
        try {

            $data = $this->redirectionRepository->findOneByProperties(['from_url' => $data['deeplink_url']], ['from_url', 'to_url']);

            return $this->sendSuccessResponse($data, 'Deeplink Redirection URL');
        } catch (\Exception $exception) {
            return $this->sendErrorResponse('Something went wrong', $exception->getMessage(), HttpStatusCode::INTERNAL_ERROR);
        }
    }
}
