<?php

/**
 * Created by PhpStorm.
 * User: bs-205
 * Date: 18/08/19
 * Time: 17:23
 */

namespace App\Services;

use App\Repositories\CorpContactUsInfoRepository;
use App\Traits\CrudTrait;

class CorpContactInfoService extends ApiBaseService
{
    use CrudTrait;

    /**
     * @var CorpContactUsInfoRepository
     */
    private $corpContactUsInfoRepository;

    /**
     * DigitalServicesService constructor.
     * @param CorpContactUsInfoRepository $corpContactUsInfoRepository
     */
    public function __construct(
        CorpContactUsInfoRepository $corpContactUsInfoRepository
    ) {
        $this->corpContactUsInfoRepository = $corpContactUsInfoRepository;
        $this->setActionRepository($corpContactUsInfoRepository);
    }

    public function storeContactInfo($data)
    {
        $this->save($data);
        return $this->sendSuccessResponse([], 'Contact Information Saved!');
    }
}
