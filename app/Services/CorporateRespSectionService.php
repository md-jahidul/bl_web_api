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
     * AlSliderService constructor.
     * @param CorporateRespSectionRepository $corporateRespSectionRepository
     */
    public function __construct(CorporateRespSectionRepository $corporateRespSectionRepository)
    {
        $this->corpRespSection = $corporateRespSectionRepository;
        $this->setActionRepository($corporateRespSectionRepository);
    }

    public function sections()
    {
        $data = $this->corpRespSection->getAll();
        return $this->sendSuccessResponse($data, 'Corporate Responsibility Section Data');
    }
}
