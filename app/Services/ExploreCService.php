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
     * AboutPageService constructor.
     * @param PriyojonRepository $priyojonRepository
     */
    public function __construct(ExploreCRepository $exploreCRepository, ComponentRepository $componentRepository)
    {
        $this->exploreCRepository = $exploreCRepository;
        $this->componentRepository = $componentRepository;
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
                return $this->sendSuccessResponse($data, 'Explore C\'s Landing Page content');
            } else {
                return response()->error("Data Not Found!");
            }
        } catch (QueryException $exception) {
            return response()->error("Something wrong", $exception);
        }
    }

}
