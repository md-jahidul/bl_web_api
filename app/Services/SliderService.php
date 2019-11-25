<?php

namespace App\Services;

use App\Enums\HttpStatusCode;
use App\Http\Resources\SliderResource;
use App\Repositories\SliderRepository;
use Exception;

/**
 * Class SliderService
 * @package App\Services
 */
class SliderService extends ApiBaseService
{

    /**
     * @var SliderRepository
     */
    protected $sliderRepository;


    /**
     * SliderService constructor.
     * @param SliderRepository $sliderRepository
     */
    public function __construct(SliderRepository $sliderRepository)
    {
        $this->sliderRepository = $sliderRepository;
    }


    /**
     * Retrieve SliderInfo
     *
     * @return string
     */
    public function getHomeSliderInfo()
    {

        try {
            $data = $this->sliderRepository->getHomeSliderInfo();

            $formatted_data = SliderResource::collection($data);

            return $this->sendSuccessResponse(
                $formatted_data,
                'Home Slider',
                [],
                HttpStatusCode::SUCCESS
            );
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }


    /**
     * Retrieve SliderInfo
     *
     * @return string
     */
    public function getDashboardSliderInfo()
    {
        try {
            $data = $this->sliderRepository->getDashboardSliderInfo();

            $formatted_data = SliderResource::collection($data);

            return $this->sendSuccessResponse(
                $formatted_data,
                'Dashboard Slider',
                [],
                HttpStatusCode::SUCCESS
            );
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }
}
