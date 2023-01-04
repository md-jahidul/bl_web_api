<?php

namespace App\Http\Controllers\API\V1;

use App\Services\MediaLandingPageService;
use App\Http\Controllers\Controller;
use App\Services\MediaPressNewsEventService;
use App\Services\MediaTvcVideoService;
use Illuminate\Http\JsonResponse;

class BlogController extends Controller
{
    /**
     * @var MediaLandingPageService
     */
    private $mediaLandingPageService;

    /**
     * @var MediaPressNewsEventService
     */
    private $mediaPressNewsEventService;

    private const REFERENCE_TYPE = "blog_landing_page";
    private const POST_REFERENCE_TYPE = "blog";

    /**
     * RolesController constructor.
     * @param MediaLandingPageService $mediaLandingPageService
     * @param MediaPressNewsEventService $mediaPressNewsEventService
     */
    public function __construct(
        MediaLandingPageService $mediaLandingPageService,
        MediaPressNewsEventService $mediaPressNewsEventService
    ) {
        $this->mediaLandingPageService = $mediaLandingPageService;
        $this->mediaPressNewsEventService = $mediaPressNewsEventService;
    }

//    public function getComponents()
//    {
//        return $this->mediaLandingPageService->landingData();
//    }

    /**
     * @return JsonResponse|mixed
     */
    public function getLandingPageDataByRefType()
    {
        return $this->mediaLandingPageService->landingDataByReferenceType(self::REFERENCE_TYPE, self::POST_REFERENCE_TYPE);
    }

    public function getBlogDetails()
    {
        return $this->mediaPressNewsEventService->detailsComponent();
    }

//    public function getPressReleaseFilter($from, $to)
//    {
//        return $this->mediaPressNewsEventService->mediaPressEventFilterData(self::PRESS_RELEASE, $from, $to);
//    }

//    /**
//     * @return JsonResponse|mixed
//     */
//    public function getNewsEvent()
//    {
//        return $this->mediaPressNewsEventService->mediaPressEventData(self::NEWS_EVENT);
//    }

//    /**
//     * @param $from
//     * @param $to
//     * @return mixed
//     */
//    public function getNewsEventFilter($from, $to)
//    {
//        return $this->mediaPressNewsEventService->mediaPressEventFilterData(self::NEWS_EVENT, $from, $to);
//    }
//
//    public function getTvcVideoData()
//    {
//        return $this->mediaTvcVideoService->getTvcVideoData();
//    }
}
