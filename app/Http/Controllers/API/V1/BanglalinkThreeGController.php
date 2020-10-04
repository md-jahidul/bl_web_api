<?php

namespace App\Http\Controllers\API\V1;

use App\Repositories\MediaTvcVideoRepository;
use App\Services\AlFaqService;
use App\Services\BanglalinkThreeGService;
use App\Services\FourGCampaignService;
use App\Services\FourGLandingPageService;
use App\Services\MediaBannerImageService;
use App\Services\MediaLandingPageService;
use App\Services\MediaPressNewsEventService;
use App\Services\MediaTvcVideoService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use phpDocumentor\Reflection\Types\Self_;

class BanglalinkThreeGController extends Controller
{
    /**
     * @var BanglalinkThreeGService
     */
    private $banglalinkThreeGService;

    /**
     * RolesController constructor.
     * @param BanglalinkThreeGService $banglalinkThreeGService
     */
    public function __construct(
        BanglalinkThreeGService $banglalinkThreeGService
    ) {
        $this->banglalinkThreeGService = $banglalinkThreeGService;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function getThreeGData()
    {
        return $this->banglalinkThreeGService->threeGdata();
    }
}
