<?php

namespace App\Http\Controllers\API\V1;

use App\Services\MediaLandingPageService;
use App\Http\Controllers\Controller;
use App\Services\MediaPressNewsEventService;
use App\Services\MediaTvcVideoService;
use Illuminate\Http\JsonResponse;

class MediaController extends Controller
{
    /**
     * @var MediaLandingPageService
     */
    private $mediaLandingPageService;
    /**
     * @var MediaTvcVideoService
     */
    private $mediaTvcVideoService;
    /**
     * @var MediaPressNewsEventService
     */
    private $mediaPressNewsEventService;

    private const PRESS_RELEASE = "press_release";
    private const NEWS_EVENT = "news_event";

    /**
     * RolesController constructor.
     * @param MediaLandingPageService $mediaLandingPageService
     * @param MediaPressNewsEventService $mediaPressNewsEventService
     * @param MediaTvcVideoService $mediaTvcVideoService
     */
    public function __construct(
        MediaLandingPageService $mediaLandingPageService,
        MediaPressNewsEventService $mediaPressNewsEventService,
        MediaTvcVideoService $mediaTvcVideoService
    ) {
        $this->mediaLandingPageService = $mediaLandingPageService;
        $this->mediaPressNewsEventService = $mediaPressNewsEventService;
        $this->mediaTvcVideoService = $mediaTvcVideoService;
    }

    public function getComponents()
    {
        return $this->mediaLandingPageService->landingData();
    }

    /**
     * @return JsonResponse|mixed
     */
    public function getPressRelease()
    {
        return $this->mediaPressNewsEventService->mediaPressEventData(self::PRESS_RELEASE);
    }

    public function getPressReleaseFilter($from, $to)
    {
        return $this->mediaPressNewsEventService->mediaPressEventFilterData(self::PRESS_RELEASE, $from, $to);
    }

    /**
     * @return JsonResponse|mixed
     */
    public function getNewsEvent()
    {
        return $this->mediaPressNewsEventService->mediaPressEventData(self::NEWS_EVENT);
    }

    /**
     * @param $from
     * @param $to
     * @return mixed
     */
    public function getNewsEventFilter($from, $to)
    {
        return $this->mediaPressNewsEventService->mediaPressEventFilterData(self::NEWS_EVENT, $from, $to);
    }

    public function getTvcVideoData()
    {
        return $this->mediaTvcVideoService->getTvcVideoData();
    }
}
