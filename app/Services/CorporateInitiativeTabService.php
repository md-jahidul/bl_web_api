<?php

/**
 * Created by PhpStorm.
 * User: bs-205
 * Date: 18/08/19
 * Time: 17:23
 */

namespace App\Services;

use App\Repositories\CorporateInitiativeTabRepository;
use App\Repositories\CorpRespContactUsRepository;
use App\Traits\CrudTrait;
use App\Traits\FileTrait;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;

class CorporateInitiativeTabService extends ApiBaseService
{
    use CrudTrait;

    /**
     * @var CorporateInitiativeTabRepository
     */
    private $initiativeTabRepository;
    /**
     * @var CorpRespContactUsRepository
     */
    private $contactUsRepository;

    /**
     * DigitalServicesService constructor.
     * @param CorporateInitiativeTabRepository $initiativeTabRepository
     * @param CorpRespContactUsRepository $contactUsRepository
     */
    public function __construct(
        CorporateInitiativeTabRepository $initiativeTabRepository,
        CorpRespContactUsRepository $contactUsRepository
    ) {
        $this->initiativeTabRepository = $initiativeTabRepository;
        $this->contactUsRepository = $contactUsRepository;
        $this->setActionRepository($initiativeTabRepository);
    }

    public function getTabs()
    {
        $tabs = $this->initiativeTabRepository->getTabs();

        $contactUsInfo = $this->contactUsRepository->getContactContent('initiative');
        $data = [
            'tabs' => $tabs,
            'contact_us' => $contactUsInfo
        ];
        return $this->sendSuccessResponse($data, 'Corporate Initiative Tabs Data!!');
    }
}
