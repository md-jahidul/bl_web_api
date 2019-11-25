<?php

namespace App\Services;

use App\Enums\HttpStatusCode;
use App\Http\Resources\AppVersionResource;
use App\Repositories\AppVersionRepository;

class AppVersionService extends ApiBaseService
{

    /**
     * @var AppVersionRepository
     */
    protected $appVersionRepository;


    /**
     * AppVersionService constructor.
     * @param AppVersionRepository $appVersionRepository
     */
    public function __construct(AppVersionRepository $appVersionRepository)
    {
         $this->appVersionRepository = $appVersionRepository;
    }


    /**
     * Version Info
     * @param $platform
     * @return mixed
     */
    public function getVersionInfo($platform)
    {
        try {
            $data = $this->appVersionRepository->getAppVersionWithPlatform($platform);
            $formatted_data = AppVersionResource::collection($data);
            return $this->sendSuccessResponse($formatted_data, 'App Version', [], HttpStatusCode::SUCCESS);
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage(), [], HttpStatusCode::INTERNAL_ERROR);
        }
    }
}
