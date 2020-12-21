<?php


namespace App\Services;


use App\Http\Resources\AppServiceResource;
use App\Repositories\AppServiceBookmarkRepository;
use App\Repositories\AppServiceCategoryRepository;
use App\Repositories\AppServiceProductRepository;
use App\Repositories\AppServiceTabRepository;
use App\Repositories\FixedPageMetaTagRepository;
use App\Traits\CrudTrait;
use Illuminate\Http\JsonResponse;

class FixedPageMetaTagService extends ApiBaseService
{
    use CrudTrait;

    /**
     * @var FixedPageMetaTagRepository
     */
    private $fixedPageMetaTagRepository;

    /**
     * AboutPageService constructor.
     * @param FixedPageMetaTagRepository $fixedPageMetaTagRepository
     */
    public function __construct(
        FixedPageMetaTagRepository $fixedPageMetaTagRepository
    ) {
        $this->fixedPageMetaTagRepository = $fixedPageMetaTagRepository;
    }

    /**
     * @return JsonResponse
     */
    public function metaTag($key)
    {
        $data = $this->fixedPageMetaTagRepository->findOneByProperties(['dynamic_route_key' => $key],
            [
                'dynamic_route_key', 'page_header', 'page_header_bn', 'schema_markup'
            ]);
        return $this->sendSuccessResponse($data,'Fixed page meta tag');
    }
}
