<?php

namespace App\Services;

use App\Http\Resources\AlBannerResource;
use App\Http\Resources\ExploreCDetailsResource;
use App\Http\Resources\ExploreCResource;
use App\Models\AlBanner;
use App\Repositories\ComponentRepository;
use App\Repositories\ExploreCRepository;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;

class ExploreCService extends ApiBaseService
{
    /**
     * @var ExploreCRepository
     */
    private $exploreCRepository;
    private $componentRepository;
    /**
     * @var FixedPageMetaTagService
     */
    private $metaTagService;


    /**
     * AboutPageService constructor.
     * @param PriyojonRepository $priyojonRepository
     */
    public function __construct(
        ExploreCRepository $exploreCRepository,
        ComponentRepository $componentRepository,
        FixedPageMetaTagService $metaTagService
    ) {
        $this->exploreCRepository = $exploreCRepository;
        $this->componentRepository = $componentRepository;
        $this->metaTagService = $metaTagService;
    }

    /**
     * @return JsonResponse|mixed
     */
    public function getExploreC()
    {
        try {
            $data = $this->exploreCRepository->getExploreC();

            if ($data) {
                $data = ExploreCResource::collection($data);
                $seoData = $this->metaTagService->getMetaByKey("explore_c");
                $data = [
                    'components' => $data,
                    'seo_data' => [
                        'page_header' => $seoData->page_header,
                        'page_header_bn' => $seoData->page_header_bn,
                        'schema_markup' => $seoData->schema_markup
                    ]
                ];
                return $this->sendSuccessResponse($data, 'Explore C\'s Landing Page content');
            } else {
                return response()->error("Data Not Found!");
            }
        } catch (QueryException $exception) {
            return response()->error("Something wrong", $exception);
        }
    }

}
