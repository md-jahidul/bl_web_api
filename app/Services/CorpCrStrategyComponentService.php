<?php

/**
 * Created by PhpStorm.
 * User: bs-205
 * Date: 18/08/19
 * Time: 17:23
 */

namespace App\Services;

use App\Repositories\AlFaqRepository;
use App\Repositories\CorpCrStrategyComponentRepository;
use App\Repositories\CorporateCrStrategySectionRepository;
use App\Repositories\MediaPressNewsEventRepository;
use App\Traits\CrudTrait;
use App\Traits\FileTrait;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

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
        $orderBy = ['column' => 'display_order', 'direction' => 'ASC'];
        $sections = $this->corpCrStrategySectionRepo->getSections();
        return $this->sendSuccessResponse($sections, 'Corporate CR Strategy');
    }
}
