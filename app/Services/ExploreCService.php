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

    public function getExploreCDetailsComponent($explore_c_slug){


        try {
            
            $explore_c = $this->exploreCRepository->findOneBySlug($explore_c_slug);

            if ($explore_c) {

                $components = $this->componentRepository->getExploreCDetailsComponent('explore_c', $explore_c->id);
                $banner = AlBanner::where(['section_id' => $explore_c->id, 'section_type' => 'explore_c'])->first();
    
                $data['explore_c_id'] =  $explore_c->id;
                $data['components'] = $components ? ExploreCDetailsResource::collection($components) : [];
                    $data['banner'] = $banner ? AlBannerResource::make($banner) : null;

                    return $this->sendSuccessResponse($data, 'Explore C\'s Details Page content');
                
            }else {
                return response()->error("Data Not Found!");
            }

        } catch (QueryException $exception) {
            return response()->error("Something wrong", $exception);
        }

    }
}
