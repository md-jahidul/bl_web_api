<?php

namespace App\Http\Controllers\API\V1;

use App\Services\MediaLandingPageService;
use App\Http\Controllers\Controller;
use App\Services\MediaPressNewsEventService;
use App\Services\MediaTvcVideoService;

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

    public function getPressRelease()
    {
        return $this->mediaPressNewsEventService->pressReleaseData();
    }

    public function getTvcVideoData()
    {
        return $this->mediaTvcVideoService->getTvcVideoData();
    }
}
