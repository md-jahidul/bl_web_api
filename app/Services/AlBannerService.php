<?php

namespace App\Services;

use App\Repositories\AlBannerRepository;

/**
 * Class AlBannerService
 * @package App\Services
 */
class AlBannerService extends ApiBaseService
{

    /**
     * @var AlBannerRepository
     */
    protected $alBannerRepository;


    /**
     * AlBannerService constructor.
     * @param AlBannerRepository $alBannerRepository
     */
    public function __construct(AlBannerRepository $alBannerRepository)
    {
        $this->alBannerRepository = $alBannerRepository;
    }

    public function getBanner($sectionId, $sectionType)
    {
        return $this->alBannerRepository->findOneByProperties(['section_id' => $sectionId, 'section_type' => $sectionType], [
            'section_id', 'section_type',
            'title_en', 'title_bn', 'desc_en', 'desc_bn', 'image', 'alt_text_en', 'alt_text_bn', 'image_name_en', 'image_name_bn'
        ]);
    }
}
