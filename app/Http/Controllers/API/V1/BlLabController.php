<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Resources\PartnerOfferResource;
use App\Http\Resources\QuickLaunchResource;
use App\Http\Resources\SliderImageResource;
use App\Models\BusinessOthers;
use App\Models\QuickLaunch;
use App\Models\QuickLaunchItem;
use App\Models\AlSlider;
use App\Models\AlSliderComponentType;
use App\Models\AlSliderImage;
use App\Models\ShortCode;
use App\Models\MetaTag;
use App\Repositories\DynamicUrlRedirectionRepository;
use App\Services\HomeService;
use App\Services\ProductService;
use App\Services\QuickLaunchService;
use App\Services\SalesAndServicesService;
use Illuminate\Database\QueryException;
use App\Http\Controllers\Controller;
use App\Repositories\BusinessTypeRepository;
use App\Services\BlLabService;
use DB;
use Validator;
use App\Services\EcareerService;
use Illuminate\Http\Request;
use Symfony\Component\Debug\Exception\FatalThrowableError;

class BlLabController extends Controller
{

    /**
     * @var BlLabService
     */
    private $blLabService;
    /**
     * BlLabController constructor.
     * @param BlLabService $blLabService
     */
    public function __construct(
        BlLabService $blLabService
    ) {
        $this->blLabService = $blLabService;
    }

    public function getBlLabPageData(Request $request)
    {
        // return $request->header('authorization');
        return $this->blLabService->getComponents($request);
    }


}
