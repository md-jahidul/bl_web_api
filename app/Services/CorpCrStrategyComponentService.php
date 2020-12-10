<?php

/**
 * Created by PhpStorm.
 * User: bs-205
 * Date: 18/08/19
 * Time: 17:23
 */

namespace App\Services;

use App\Repositories\CorpCrStrategyComponentRepository;
use App\Repositories\CorporateCrStrategySectionRepository;
use App\Repositories\CorpRespContactUsRepository;
use App\Traits\CrudTrait;
use App\Traits\FileTrait;

class CorpCrStrategyComponentService extends ApiBaseService
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
     * DigitalServicesService constructor.
     * @param CorpCrStrategyComponentRepository $corpCrStrategyComponentRepository
     * @param CorporateCrStrategySectionRepository $corporateCrStrategySectionRepository
     */
    public function __construct(
        CorpCrStrategyComponentRepository $corpCrStrategyComponentRepository,
        CorporateCrStrategySectionRepository $corporateCrStrategySectionRepository
    ) {
        $this->corpCrStrategyComponentRepo = $corpCrStrategyComponentRepository;
        $this->corpCrStrategySectionRepo = $corporateCrStrategySectionRepository;
        $this->setActionRepository($corpCrStrategyComponentRepository);
    }

    public function crStrategySection()
    {
        $sections = $this->corpCrStrategySectionRepo->getSections();
        return $this->sendSuccessResponse($sections, 'Corporate CR Strategy Data!');
    }

    public function getComponentWithDetails($urlSlug)
    {
        $components = $this->corpCrStrategyComponentRepo->componentWithDetails($urlSlug);
        ($components) ? $components : $data = json_decode("{}");
        return $this->sendSuccessResponse($components, 'Corporate CR Strategy Details Components Data!');
    }
}
