<?php

/**
 * Created by PhpStorm.
 * User: bs-205
 * Date: 18/08/19
 * Time: 17:23
 */

namespace App\Services;

use App\Repositories\CorpContactUsInfoRepository;
use App\Repositories\CorpCrStrategyComponentRepository;
use App\Repositories\CorporateCrStrategySectionRepository;
use App\Repositories\CorpRespContactUsRepository;
use App\Traits\CrudTrait;
use App\Traits\FileTrait;

class CorpContactInfoService extends ApiBaseService
{
    use CrudTrait;
    use FileTrait;

    /**
     * @var CorpCrStrategyComponentRepository
     */
    private $corpCrStrategyComponentRepo;
    /**
     * @var CorporateCrStrategySectionRepository
     */
    private $corpCrStrategySectionRepo;
    /**
     * @var CorpRespContactUsRepository
     */
    private $contactUsRepository;
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

    public function storeContactInfo($data, $pageSlug)
    {
        $data['page_slug'] = $pageSlug;
        $this->save($data);
        return $this->sendSuccessResponse([], 'Contact Information Saved!');
    }
}
