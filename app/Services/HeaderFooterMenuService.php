<?php

namespace App\Services;

use App\Enums\HttpStatusCode;
use App\Http\Resources\BannerResource;
use App\Models\FrontEndDynamicRoute;
use App\Repositories\AdTechRepository;
use App\Repositories\BannerRepository;
use App\Repositories\ConfigRepository;
use App\Repositories\FooterMenuRepository;
use App\Repositories\MenuRepository;
use App\Repositories\SubFooterRepository;
use Exception;
use Illuminate\Http\JsonResponse;

/**
 * Class BannerService
 * @package App\Services
 */
class HeaderFooterMenuService extends ApiBaseService
{

    /**
     * @var ApiBaseService
     */
    protected $apiBaseService;

    /**
     * @var MenuRepository
     */
    protected $menuRepository;
    protected $footerMenuRepository;
    protected $configRepository;
    /**
     * @var AdTechRepository
     */
    private $adTechRepository;
    protected $subFooterRepository;


    /**
     * HeaderFooterMenuService constructor.
     * @param MenuRepository $menuRepository
     * @param FooterMenuRepository $footerMenuRepository
     * @param ConfigRepository $configRepository
     * @param ApiBaseService $apiBaseService
     */
    public function __construct(
        MenuRepository $menuRepository,
        FooterMenuRepository $footerMenuRepository,
        ConfigRepository $configRepository,
        ApiBaseService $apiBaseService,
        AdTechRepository $adTechRepository,
        SubFooterRepository $subFooterRepository
    )
    {
        $this->menuRepository = $menuRepository;
        $this->footerMenuRepository = $footerMenuRepository;
        $this->configRepository = $configRepository;
        $this->adTechRepository = $adTechRepository;
        $this->apiBaseService = $apiBaseService;
        $this->subFooterRepository = $subFooterRepository;
    }

    public function pagesInfo()
    {
//        $pages = $this->footerMenuRepository
//            ->findByProperties(['is_dynamic_page' => 1], ['id', 'en_label_text', 'url', 'dynamic_page_slug']);
//        foreach ($pages as $pageData){
//            $data[] = [
//                'id' => $pageData->id,
//                'title' => $pageData->en_label_text,
//                'code' => 'DynamicPages',
//                'url' => $pageData->url,
//                'exact' => true,
//                'slug' => $pageData->dynamic_page_slug,
//            ];
//        }
        $routes = FrontEndDynamicRoute::all('id', 'code', 'url', 'key', 'exact', 'children');
        foreach ($routes as $pageData){
            $data[] = [
                'id' => $pageData->id,
                'code' => $pageData->code,
                'url' => $pageData->url,
                'key' => $pageData->key,
                'exact' => ($pageData->exact) ? true : false,
                'children' => $pageData->children,
            ];
        }
        return isset($data) ? $data : [];
    }

    /**
     * @return JsonResponse
     */
    public function headerFooterMenus()
    {
        $headerMenus = $this->menuRepository->headerMenus();
        $headerItems = $this->configRepository->headerSettings();
        $footerMenu = $this->footerMenuRepository->footerMenu();
        $adTech = $this->adTechRepository->findOneByProperties(
            ['reference_type' => 'header-menu', 'status' => 1], ['img_url', 'is_external_url', 'redirect_url_en', 'external_url']
        );
        $subFooter = $this->subFooterRepository->getData();

        $headerSettings = [];
        foreach ($headerItems as $settings) {
            $headerSettings[$settings->key] = $settings->value;
        }

        $footerSettingsItems = $this->configRepository->whereNotIn();

        $footer_settings = [];
        foreach ($footerSettingsItems as $settings) {
            $footer_settings[$settings->key] = $settings->value;
        }

        if (isset($footerMenu) && isset($headerMenus)) {
            $result = [
                'header' => [
                    'menu' => $headerMenus,
                    'settings' => $this->configRepository->resourceData($headerSettings),
                    'ad_tech' => $adTech
                ],
                'footer' => [
                    'menu' => $footerMenu,
                    'settings' => $footer_settings
                ],
                'sub_footer' => [
                    'data' => $subFooter
                ],
                'dynamic_routes' => $this->pagesInfo()
            ];
            return $this->apiBaseService->sendSuccessResponse($result, 'Data Found Header Footer Menus!');
        }

        return $this->apiBaseService->sendErrorResponse('Data not Found!', '', HttpStatusCode::NOT_FOUND);
    }
}
