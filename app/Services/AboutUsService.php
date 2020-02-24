<?php

namespace App\Services;

use App\Enums\HttpStatusCode;
use App\Http\Resources\AboutUsEcareerResource;
use App\Http\Resources\AboutUsResource;
use App\Http\Resources\ManagementResource;
use App\Http\Resources\SliderImageResource;
use App\Repositories\AboutUsRepository;
use App\Repositories\EcarrerPortalRepository;
use App\Repositories\ManagementRepository;
use App\Repositories\SliderImageRepository;
use App\Repositories\SliderRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

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

    /**
     * @var EcarrerPortalRepository
     */
    protected $eCareerRepository;

    /**
     * @var SliderRepository
     */
    protected $sliderRepository;
    /**
     * @var SliderImageRepository
     */
    private $sliderImageRepository;


    /**
     * AboutUsService constructor.
     * @param AboutUsRepository $aboutUsRepository
     * @param ManagementRepository $managementRepository
     * @param EcarrerPortalRepository $eCareerRepository
     * @param SliderRepository $sliderRepository
     * @param SliderImageRepository $sliderImageRepository
     */
    public function __construct(AboutUsRepository $aboutUsRepository,
        ManagementRepository $managementRepository,
        EcarrerPortalRepository $eCareerRepository,
        SliderRepository $sliderRepository,
        SliderImageRepository $sliderImageRepository
)
    {
        $this->aboutUsRepository = $aboutUsRepository;
        $this->managementRepository = $managementRepository;
        $this->eCareerRepository = $eCareerRepository;
        $this->sliderRepository = $sliderRepository;
        $this->sliderImageRepository = $sliderImageRepository;
    }


    /**
     * @return JsonResponse
     */
    public function getAboutBanglalink()
    {
        try {
            $sliderData = $this->sliderRepository->getSliderInfo('about_media');
            $sliderImage = $this->sliderImageRepository->findByProperties(['slider_id' => $sliderData->id]);
            $sliderImage = SliderImageResource::collection($sliderImage);

            $data = $this->aboutUsRepository->getAboutBanglalink();
            $formatted_data = AboutUsResource::collection($data);
            $component['banner'] = $formatted_data;
            $component['slider'] = [ 'slider_data' => $sliderData, 'slider_images' => $sliderImage];
            return $this->sendSuccessResponse($component, 'About Banglalink', [], HttpStatusCode::SUCCESS);
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage(), [], HttpStatusCode::INTERNAL_ERROR);
        }
    }

    /**
     * @return JsonResponse
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

    /**
     * @return JsonResponse
     */
    public function getEcareersInfo()
    {
        try {
            $data = $this->eCareerRepository->getEcareersInfo();
            $formatted_data = AboutUsEcareerResource::collection($data);
            return $this->sendSuccessResponse($formatted_data, 'Banglalink eCareer', [], HttpStatusCode::SUCCESS);
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage(), [], HttpStatusCode::INTERNAL_ERROR);
        }
    }
}
