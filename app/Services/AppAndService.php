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

    /**
     * @return JsonResponse
     */
    public function appServiceData()
    {
        $data = $this->appServiceTabRepository->appServiceCollection();

        return $this->sendSuccessResponse($data,'Internet packs list', config('filesystems.image_host_url'));
    }
}
