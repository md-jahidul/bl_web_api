<?php

/**
 * Created by PhpStorm.
 * User: bs-205
 * Date: 18/08/19
 * Time: 17:23
 */

namespace App\Services;

use App\Repositories\CorporateInitiativeTabRepository;
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
     * DigitalServicesService constructor.
     * @param CorporateInitiativeTabRepository $initiativeTabRepository
     */
    public function __construct(CorporateInitiativeTabRepository $initiativeTabRepository)
    {
        $this->initiativeTabRepository = $initiativeTabRepository;
        $this->setActionRepository($initiativeTabRepository);
    }

    public function getTabs()
    {
        $data = $this->initiativeTabRepository->getTabs();
        return $this->sendSuccessResponse($data, 'Corporate Initiative Tabs Data!!');
    }
}
