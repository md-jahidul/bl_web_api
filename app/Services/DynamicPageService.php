<?php

/**
 * Created by PhpStorm.
 * User: BS23
 * Date: 27-Aug-19
 * Time: 3:56 PM
 */

namespace App\Services;

use App\Repositories\ComponentRepository;
use App\Repositories\DynamicPageRepository;
use App\Repositories\FooterMenuRepository;
use App\Services\Assetlite\ComponentService;
use App\Traits\CrudTrait;
use App\Traits\FileTrait;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;

class DynamicPageService extends ApiBaseService
{
    use CrudTrait;
    use FileTrait;

    /**
     * @var $prizeService
     */
    protected $pageRepo;

    protected $componentRepository;

    protected $footerMenuRepository;

    protected const PageType = "other_dynamic_page";

    /**
     * DynamicPageService constructor.
     * @param DynamicPageRepository $pageRepo
     * @param ComponentRepository $componentRepository
     * @param FooterMenuRepository $footerMenuRepository
     */
    public function __construct(
        DynamicPageRepository $pageRepo,
        ComponentRepository $componentRepository,
        FooterMenuRepository $footerMenuRepository
    )
    {
        $this->pageRepo = $pageRepo;
        $this->componentRepository = $componentRepository;
        $this->footerMenuRepository = $footerMenuRepository;
        $this->setActionRepository($pageRepo);
    }


    public function pageData($slug)
    {
        $pageData = $this->pageRepo->page(strtolower($slug));

        if (!empty($pageData)) {

            foreach ($pageData->components as $key => $value) {
                if ($value->component_type == 'button_component') {
    
                    unset($value->title_en);
                    unset($value->title_bn);
                    unset($value->extra_title_bn);
                    unset($value->extra_title_en);
                    unset($value->multiple_attributes);
                    unset($value->video);
                    unset($value->image);
                    unset($value->alt_text);
                    unset($value->other_attributes);
                }
            }
        }
        return $this->sendSuccessResponse($pageData, 'Dynamic page data');
    }
}
