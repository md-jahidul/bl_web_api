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
     * @var $imageFileViewerService
     */
    private $imageFileViewerService;

    /**
     * AboutPageService constructor.
     * @param PriyojonRepository $priyojonRepository
     */
    public function __construct(
        PriyojonRepository $priyojonRepository,
        ImageFileViewerService $imageFileViewerService
    ) {
        $this->priyojonRepository = $priyojonRepository;
        $this->imageFileViewerService = $imageFileViewerService;
    }

    /**
     * @return JsonResponse|mixed
     */
    public function headerMenu()
    {
        $data = $this->priyojonRepository->findBy(['parent_id' => 0, 'status' => 1], ['children' => function($q){
            $q->where('status', 1)
              ->select('parent_id', 'title_en', 'title_bn', 'url', 'url_slug_en', 'url_slug_bn', 'alias');
        }], ['id', 'title_en', 'title_bn', 'banner_image_url', 'banner_mobile_view', 'alt_text_en',
            'alt_text_bn', 'banner_name', 'banner_name_bn']);

        $keyData = config('filesystems.moduleType.Priyojon');
        foreach ($data as $key => $vaule) {
            $val = array_merge($vaule->toArray(), $this->imageFileViewerService->prepareImageData($vaule, $keyData));
            unset($val['banner_image_url'], $val['banner_mobile_view']);
            $data[$key] = $val;
        }

        return $this->sendSuccessResponse($data, 'Priyojon Landing Page Header Menu');
    }
}
