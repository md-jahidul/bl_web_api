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

        return $sections;

        $data = [];
        foreach ($sections as $section){
            $query = ['page_type' => 'cr_strategy_section', 'page_id' => $section->id];
            $column = [
                        'title_en',
                        'title_bn', 'details_en',
                        'details_bn', 'other_attributes',
                        'url_slug_en', 'page_header', 'schema_markup'
                    ];
            $components = $this->corpCrStrategyComponentRepo->findByProperties($query, $column);
            $data[] = [
                'title_en' => $section->title_en,
                'title_bn' => $section->title_bn,
                'section_type' => $section->section_type,
                'components' => $components
            ];
        }

//        dd($data);

        return $this->sendSuccessResponse($data, 'Corporate CR Strategy');
    }
}
