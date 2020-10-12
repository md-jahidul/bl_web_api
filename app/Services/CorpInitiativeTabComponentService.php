<?php

namespace App\Services;

use App\Repositories\CorpInitiativeTabComponentRepository;
use App\Repositories\CorporateInitiativeTabRepository;
use App\Traits\CrudTrait;
use App\Traits\FileTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class CorpInitiativeTabComponentService extends ApiBaseService
{
    use CrudTrait;
    use FileTrait;

    /**
     * @var CorpInitiativeTabComponentRepository
     */
    private $tabComponentRepository;
    /**
     * @var CorporateInitiativeTabRepository
     */
    private $initiativeTabRepository;

    /**
     * AppServiceProductService constructor.
     * @param CorporateInitiativeTabRepository $initiativeTabRepository
     * @param CorpInitiativeTabComponentRepository $tabComponentRepository
     */
    public function __construct(
        CorporateInitiativeTabRepository $initiativeTabRepository,
        CorpInitiativeTabComponentRepository $tabComponentRepository
    ) {
        $this->initiativeTabRepository = $initiativeTabRepository;
        $this->tabComponentRepository = $tabComponentRepository;
        $this->setActionRepository($tabComponentRepository);
    }


    /**
     * @param $slug
     * @return JsonResponse|mixed
     */
    public function getTabComponents($slug)
    {
        $tabId = $this->initiativeTabRepository->findOneByProperties(['url_slug_en' => $slug], ['id', 'url_slug_en']);
        $tabId = isset($tabId) ? $tabId->id : null;
        $data = $this->tabComponentRepository->findByProperties(['initiative_tab_id' => $tabId],
            [
                'initiative_tab_id', 'component_type', 'component_title_en', 'component_title_bn',
                'title_en', 'title_bn', 'editor_en', 'editor_bn', 'multiple_attributes'
            ]);
        return $this->sendSuccessResponse($data, "Corporate Initiative Tab Component");
    }

}
