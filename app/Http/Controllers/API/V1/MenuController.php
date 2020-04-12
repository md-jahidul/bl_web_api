<?php

namespace App\Http\Controllers\API\V1;


use App\Http\Controllers\Controller;
use App\Http\Resources\ConfigResource;
use App\Models\FooterMenu;
use App\Models\Menu;
use App\Models\Config;
use App\Services\HeaderFooterMenuService;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;

class MenuController extends Controller
{
    protected $headerFooterMenuService;

    public function __construct(HeaderFooterMenuService $headerFooterMenuService)
    {
        $this->headerFooterMenuService = $headerFooterMenuService;
    }


    /**
     * @return JsonResponse
     */
    public function getHeaderFooterMenus()
    {
        return $this->headerFooterMenuService->headerFooterMenus();
    }
}
