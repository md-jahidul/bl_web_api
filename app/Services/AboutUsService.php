<?php

namespace App\Services;

use App\Enums\HttpStatusCode;
use App\Http\Resources\AboutUsEcareerResource;
use App\Http\Resources\AboutUsResource;
use App\Http\Resources\ManagementResource;
use App\Http\Resources\SliderImageResource;
use App\Repositories\AboutUsEcareerRepository;
use App\Repositories\AboutUsRepository;
use App\Repositories\EcareerPortalRepository;
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
     * @var AboutUsEcareerRepository
     */
    protected $aboutUsEcareerRepository;

    /**
     * @var SliderRepository
     */
    protected $sliderRepository;
    /**
     * @var SliderImageRepository
     */
    private $sliderImageRepository;

    /**
     * @var ImageFileViewerService
     */
    private $imageFileViewerService;


    /**
     * AboutUsService constructor.
     * @param AboutUsRepository $aboutUsRepository
     * @param ManagementRepository $managementRepository
     * @param AboutUsEcareerRepository $aboutUsEcareerRepository
     * @param SliderRepository $sliderRepository
     * @param SliderImageRepository $sliderImageRepository
     * @param ImageFileViewerService $imageFileViewerService
     */
    public function __construct(
        AboutUsRepository $aboutUsRepository,
        ManagementRepository $managementRepository,
        AboutUsEcareerRepository $aboutUsEcareerRepository,
        SliderRepository $sliderRepository,
        SliderImageRepository $sliderImageRepository,
        ImageFileViewerService $imageFileViewerService
    ) {
        $this->aboutUsRepository = $aboutUsRepository;
        $this->managementRepository = $managementRepository;
        $this->aboutUsEcareerRepository = $aboutUsEcareerRepository;
        $this->sliderRepository = $sliderRepository;
        $this->sliderImageRepository = $sliderImageRepository;
        $this->imageFileViewerService = $imageFileViewerService;
    }


    /**
     * @return JsonResponse
     */
    public function getAboutBanglalink()
    {
        try {
            $sliderData = $this->sliderRepository->getSliderInfo('about_media');
            $sliderImages = $this->getSliderImagesFormattedData($sliderData->id);
            $formatted_data = $this->getAboutBanglalinkFormattedData();

            $component['banner'] = $formatted_data;
            $component['slider'] = [ 'slider_data' => $sliderData, 'slider_images' => $sliderImages];

            return $this->sendSuccessResponse($component, 'About Banglalink', [], HttpStatusCode::SUCCESS);
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage(), [], HttpStatusCode::INTERNAL_ERROR);
        }
    }

    /**
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getAboutBanglalinkFormattedData()
    {
        $abouts = $this->aboutUsRepository->getAboutBanglalink();

        $data = [];

        foreach ($abouts as $key => $about) {
            $bannerKeyData = config('filesystems.moduleType.AboutUsBanglalink');
            $contentKeyData = config('filesystems.moduleType.AboutUsBanglalinkContent');

            $bannerImgData = $this->imageFileViewerService->prepareImageData($about, $bannerKeyData);
            $contentImgData = $this->imageFileViewerService->prepareImageData($about, $contentKeyData);

            $data[$key] = array_merge($about->toArray(), $bannerImgData);
            $data[$key] = (object) array_merge($data[$key], $contentImgData);
        }

        return AboutUsResource::collection(collect($data));
    }

    /**
     * @param $sliderInfoId
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getSliderImagesFormattedData($sliderInfoId)
    {
        $sliderImages = $this->sliderImageRepository->aboutUsSliders($sliderInfoId);

        $sliderKeyData = config('filesystems.moduleType.AlSliderImage');

        $data = [];

        foreach ($sliderImages as $key => $slider) {

            $imgData = $this->imageFileViewerService->prepareImageData($slider, $sliderKeyData);

            $data[$key] = (object) array_merge($slider->toArray(), $imgData);
        }

        return SliderImageResource::collection(collect($data));
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

            $data = $this->aboutUsEcareerRepository->getEcareersInfo();
            $formatted_data = [];

            if( $data != null){
                $arr_data = AboutUsEcareerResource::make($data);
                $formatted_data = json_decode (json_encode ($arr_data), FALSE);
            }

            return $this->sendSuccessResponse( $formatted_data, 'Banglalink eCareer', [], HttpStatusCode::SUCCESS);
        } catch (Exception $exception) {
            return $this->sendErrorResponse($exception->getMessage(), [], HttpStatusCode::INTERNAL_ERROR);
        }
    }
}
