<?php

namespace App\Services;

use App\Http\Resources\AlBannerResource;
use App\Http\Resources\ExploreCDetailsResource;
// use App\Http\Resources\ExploreCResource;
use App\Models\AlBanner;
use App\Repositories\ComponentRepository;
use App\Repositories\ExploreCDetailsRepository;
// use App\Repositories\ExploreCRepository;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;

class ExploreCDetailsService extends ApiBaseService
{
    /**
     * @var ExploreCRepository
     */
    private $exploreCDetailsRepository;
    private $componentRepository;


    /**
     * AboutPageService constructor.
     * @param PriyojonRepository $priyojonRepository
     */
    public function __construct(ExploreCDetailsRepository $exploreCDetailsRepository, ComponentRepository $componentRepository)
    {
        $this->exploreCDetailsRepository = $exploreCDetailsRepository;
        $this->componentRepository = $componentRepository;
    }

    public function getExploreCDetailsComponent($explore_c_slug){


        try {
            
            $explore_c_page = $this->exploreCDetailsRepository->findOneBySlug($explore_c_slug);

            if ($explore_c_page) {

                $components = $this->componentRepository->getExploreCDetailsComponent('explore_c', $explore_c_page->id);
                $banner = AlBanner::where(['section_id' => $explore_c_page->id, 'section_type' => 'explore_c'])->first();
    
                // $data['explore_c_id'] =  $explore_c->id;
                $data['components'] = $components ? ExploreCDetailsResource::collection($components) : [];
                $data['banner'] = $banner ? AlBannerResource::make($banner) : null;
                $data['pageInfo'] = $explore_c_page ? $explore_c_page : null;

                return $this->sendSuccessResponse($data, 'Explore C\'s Details Page content');
                
            }else {
                return response()->error("Data Not Found!");
            }

        } catch (QueryException $exception) {
            return response()->error("Something wrong", $exception);
        }

    }
}
