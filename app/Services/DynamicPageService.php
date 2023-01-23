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
use App\Services\ComponentService;
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

        foreach ($pageData->components as $key => $value) {
            if ($value->component_type == 'button_component') {
                $check_external = '';
                $link_en = '#';
                $link_bn = '#';

                if (isset($value->other_attributes['is_external_url'])) {

                    if ($value->other_attributes['is_external_url'] == 1) {
                        $check_external = 'target="_blank"';
                        # code...
                        $link_en = $link_bn = (isset($value->other_attributes['external_url'])) ? $value->other_attributes['external_url'] : '';
                    }else{

                        $link_en = (isset($value->other_attributes['redirect_url_en'])) ? $value->other_attributes['redirect_url_en'] : '';
                        $link_bn = (isset($value->other_attributes['redirect_url_bn'])) ? $value->other_attributes['redirect_url_bn'] : '';
                    }

                }

                $btn_html_en = '<a class="btn btn-danger" href="'.$link_en.'"'.$check_external.'  >'.$value->title_en.'</a>';
                $btn_html_bn = '<a class="btn btn-danger" href="'.$link_bn.'"'.$check_external.'  >'.$value->title_bn.'</a>';


                $value->button_en = $btn_html_en;
                $value->button_bn = $btn_html_bn;

                unset($value->title_en);
                unset($value->title_bn);
                unset($value->editor_en);
                unset($value->editor_bn);
                unset($value->extra_title_bn);
                unset($value->extra_title_en);
                unset($value->multiple_attributes);
                unset($value->video);
                unset($value->image);
                unset($value->alt_text);
                unset($value->other_attributes);
            }
        }
        return $this->sendSuccessResponse($pageData, 'Dynamic page data');
    }
}
