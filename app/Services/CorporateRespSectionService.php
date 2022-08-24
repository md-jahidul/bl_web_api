<?php

namespace App\Services;

use App\Repositories\AlSliderRepository;
use App\Repositories\CorporateRespSectionRepository;
use App\Repositories\SliderRepository;
use App\Traits\CrudTrait;
use App\Traits\FileTrait;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class CorporateRespSectionService extends ApiBaseService
{
    use CrudTrait;
    use FileTrait;

    /**
     * @var CorporateRespSectionRepository
     */
    private $corpRespSection;
    /**
     * @var ImageFileViewerService
     */
    private $fileViewerService;

    /**
     * AlSliderService constructor.
     * @param CorporateRespSectionRepository $corporateRespSectionRepository
     * @param ImageFileViewerService $fileViewerService
     */
    public function __construct(
        CorporateRespSectionRepository $corporateRespSectionRepository,
        ImageFileViewerService $fileViewerService
    ) {
        $this->corpRespSection = $corporateRespSectionRepository;
        $this->fileViewerService = $fileViewerService;
        $this->setActionRepository($corporateRespSectionRepository);
    }

    public function sections()
    {
        $data = $this->corpRespSection->findByProperties(['status' => 1]);
        $keyData = config('filesystems.moduleType.CorpResponsibilityTab');
        $corResTabs = array_map(function ($value) use ($keyData){
            $imgData = $this->fileViewerService->prepareImageData($value, $keyData);
            unset($value['banner_image_url']);
            unset($value['banner_mobile_view']);
            unset($value['banner_image_name']);
            unset($value['banner_image_name_bn']);
            unset($value['created_at']);
            unset($value['updated_at']);
            unset($value['status']);
            return array_merge($value, $imgData);
        }, $data->toArray());

        return $this->sendSuccessResponse($corResTabs, 'Corporate Responsibility Section Data');
    }
}
