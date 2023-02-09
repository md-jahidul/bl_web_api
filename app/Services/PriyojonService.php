<?php

namespace App\Services;

use App\Repositories\FaqCategoryRepository;
use App\Repositories\FaqRepository;
use App\Repositories\PriyojonRepository;
use Illuminate\Http\JsonResponse;

class PriyojonService extends ApiBaseService
{
    /**
     * @var FaqCategoryRepository
     */
    private $faqCatRepository;
    /**
     * @var PriyojonRepository
     */
    private $priyojonRepository;

    /**
     * AboutPageService constructor.
     * @param PriyojonRepository $priyojonRepository
     */
    public function __construct(PriyojonRepository $priyojonRepository)
    {
        $this->priyojonRepository = $priyojonRepository;
    }

    /**
     * @return JsonResponse|mixed
     */
    public function headerMenu()
    {
        $data = $this->priyojonRepository->landingPageBannerAndContent();
        return $this->sendSuccessResponse($data, 'Priyojon Landing Page Header Menu');
        $data = $this->priyojonRepository->findBy(['parent_id' => 0, 'status' => 1, 'component_type' => null], ['children' => function($q){
            $q->where('status', 1)
              ->select('parent_id', 'title_en', 'title_bn', 'url', 'url_slug_en', 'url_slug_bn', 'page_header', 'page_header_bn', 'schema_markup', 'alias');
        }], ['id', 'title_en', 'title_bn', 'banner_image_url', 'banner_mobile_view', 'alt_text_en']);

    }
}
