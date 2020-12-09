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
        $data = $this->priyojonRepository->findBy(['parent_id' => 0, 'status' => 1], ['children' => function($q){
            $q->where('status', 1)
              ->select('parent_id', 'title_en', 'title_bn', 'url');
        }], ['id', 'title_en', 'title_bn', 'banner_image_url', 'banner_mobile_view', 'alt_text_en']);

        return $this->sendSuccessResponse($data, 'Priyojon Landing Page Header Menu');
    }
}
