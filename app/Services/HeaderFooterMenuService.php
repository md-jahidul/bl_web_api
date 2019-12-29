<?php

namespace App\Services;

use App\Enums\HttpStatusCode;
use App\Http\Resources\BannerResource;
use App\Repositories\BannerRepository;
use App\Repositories\ConfigRepository;
use App\Repositories\FooterMenuRepository;
use App\Repositories\MenuRepository;
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
        ApiBaseService $apiBaseService
    )
    {
        $this->menuRepository = $menuRepository;
        $this->footerMenuRepository = $footerMenuRepository;
        $this->configRepository = $configRepository;
        $this->apiBaseService = $apiBaseService;
    }

    /**
     * @return JsonResponse
     */
    public function headerFooterMenus()
    {

        $headerMenus = $this->menuRepository->headerMenus();
        $headerItems = $this->configRepository->headerSettings();
        $footerMenu = $this->footerMenuRepository->footerMenu();

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
                    'settings' => $this->configRepository->resourceData($headerSettings)
                ],
                'footer' => [
                    'menu' => $footerMenu,
                    'settings' => $footer_settings
                ]
            ];
            return $this->apiBaseService->sendSuccessResponse($result, 'Data Found Header Footer Menus!');
        }

        return $this->apiBaseService->sendErrorResponse('Data not Found!', '', HttpStatusCode::NOT_FOUND);
    }
}
