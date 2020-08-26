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
        return $this->sendSuccessResponse($pageData, 'Dynamic page data');
    }
}
