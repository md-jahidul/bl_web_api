<?php


namespace App\Services;


use App\Http\Resources\AppServiceResource;
use App\Repositories\AppServiceCategoryRepository;
use App\Repositories\AppServiceProductRepository;
use App\Repositories\AppServiceTabRepository;
use App\Traits\CrudTrait;
use Illuminate\Http\JsonResponse;

class AppAndService extends ApiBaseService
{
    use CrudTrait;

    /**
     * @var $aboutPageRepository
     */
    protected $appServiceTabRepository;
    protected $appServiceCategoryRepository;
    protected $appServiceProductRepository;

    /**
     * AboutPageService constructor.
     * @param AppServiceTabRepository $appServiceTabRepository
     * @param AppServiceCategoryRepository $appServiceCategoryRepository
     * @param AppServiceProductRepository $appServiceProductRepository
     */
    public function __construct(
        AppServiceTabRepository $appServiceTabRepository,
        AppServiceCategoryRepository $appServiceCategoryRepository,
        AppServiceProductRepository $appServiceProductRepository
    ) {
        $this->appServiceTabRepository = $appServiceTabRepository;
        $this->appServiceCategoryRepository = $appServiceCategoryRepository;
        $this->appServiceProductRepository = $appServiceProductRepository;
    }


//    public function makeResource($requests) {
//        {
//
//
//
//            $result = [];
//            foreach ($requests as $request) {
//
//                $data['id'] =  $request->id ?? null;
//                $data['name_en'] =  $request->name_en ?? null;
//                $data['name_bn'] =  $request->name_bn ?? null;
//                $data['banner_image_url'] = config('filesystems.image_host_url') . $request->banner_image_url ?? null;
//                $data['banner_alt_text'] =  $request->banner_alt_text ?? null;
//                $data['alias'] =  $request->alias ?? null;
//
////                dd($request['categories']);
//
//                $data['categories'] = [];
//                foreach ($request['categories'] as $key => $cat) {
//                    dd($cat);
//                    $data['categories'][] = $cat->id ?? null;
//                    $data['categories'][] = $cat->id ?? null;
////                    dd($cat);
//                }
//
//                dd($data);
//
//
//                $data = [];
//                $data["id"] = $request->id ?? null;
//                $data["slider_id"] = $request->slider_id ?? null;
//                $data["title_en"] = $request->title_en ?? null;
//                $data["title_bn"] = $request->title_bn ?? null;
//                $data["start_date"] = $request->start_date ?? null;
//                $data["end_date"] = $request->end_date ?? null;
//                $data["image_url"] = config('filesystems.image_host_url') . $request->image_url;
//                $data["mobile_view_img"] = ($request->mobile_view_img) ? config('filesystems.image_host_url') . $request->mobile_view_img : null;
//                $data["alt_text"] = $request->alt_text ?? null;
//                $data["display_order"] = $request->display_order ?? null;
//                $data["is_active"] = $request->is_active ?? null;
//                if ($request->other_attributes){
//                    foreach ($request->other_attributes as $key => $value) {
//                        $data[$key] = $value;
//                    }
//                }
//
//                array_push($result, (object)$data);
//            }
//            return  $result;
//        }
//    }


    /**
     * @return JsonResponse
     */
    public function appServiceData()
    {
        $data = $this->appServiceTabRepository->appServiceCollection();

//        $resource = $this->makeResource($data);

//        dd($resource);

        return $this->sendSuccessResponse($data,'Internet packs list', config('filesystems.image_host_url'));
    }
}
