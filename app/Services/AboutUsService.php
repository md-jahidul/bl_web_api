<?php

namespace App\Services;

use App\Enums\HttpStatusCode;
use App\Http\Resources\AboutUsResource;
use App\Http\Resources\ManagementResource;
use App\Repositories\AboutUsRepository;
use App\Repositories\EcareerRepository;
use App\Repositories\ManagementRepository;

class AboutUsService extends ApiBaseService
{

    /**
     * @var AboutUsRepository
     */
    protected $aboutUsRepository;

    /**
     * @var ManagementRepository
     */
    protected $managementRepository;

    protected $eCareerRepository;


    /**
     * AboutUsService constructor.
     * @param AboutUsRepository $aboutUsRepository
     * @param ManagementRepository $managementRepository
     * @param EcareerRepository $eCareerRepository
     */
    public function __construct(AboutUsRepository $aboutUsRepository,
        ManagementRepository $managementRepository,
        EcareerRepository $eCareerRepository
)
    {
        $this->aboutUsRepository = $aboutUsRepository;
        $this->managementRepository = $managementRepository;
        $this->eCareerRepository = $eCareerRepository;
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAboutBanglalink()
    {
        try {
            $data = $this->aboutUsRepository->getAboutBanglalink();
            $formatted_data = AboutUsResource::collection($data);
            return $this->sendSuccessResponse($formatted_data, 'About Banglalink', [], HttpStatusCode::SUCCESS);
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage(), [], HttpStatusCode::INTERNAL_ERROR);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAboutManagement()
    {
        try {
            $data = $this->managementRepository->getAboutManagement();
            $formatted_data = ManagementResource::collection($data);
            return $this->sendSuccessResponse($formatted_data, 'Banglalink Management', [], HttpStatusCode::SUCCESS);
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage(), [], HttpStatusCode::INTERNAL_ERROR);
        }
    }

    public function getEcareersInfo()
    {
        try {
            $data = $this->eCareerRepository->getEcareersInfo();
           // $formatted_data = ManagementResource::collection($data);
            return $this->sendSuccessResponse($data, 'Banglalink eCareer', [], HttpStatusCode::SUCCESS);
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage(), [], HttpStatusCode::INTERNAL_ERROR);
        }
    }
}
